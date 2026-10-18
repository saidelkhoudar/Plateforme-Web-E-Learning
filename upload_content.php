<?php
require_once __DIR__ . '/includes/security.php';
require_once __DIR__ . '/config/database.php';

header('Content-Type: application/json; charset=utf-8');

// Vérification de la connexion et du rôle de l'utilisateur
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'professor') {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Accès non autorisé']);
    exit();
}

// Protection CSRF
if (!csrf_verify($_POST['csrf_token'] ?? null)) {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Jeton CSRF invalide']);
    exit();
}

// Vérification de la présence du fichier et des données requises
if (!isset($_FILES['file']) || !isset($_POST['title'], $_POST['module_id'], $_POST['content_type'])) {
    echo json_encode(['success' => false, 'message' => 'Données incomplètes']);
    exit();
}

$module_id = (int) $_POST['module_id'];
$content_type = $_POST['content_type'];
$title = trim($_POST['title']);
$description = trim($_POST['description'] ?? '');

// Type de contenu autorisé
$allowed_content_types = ['course', 'exercise', 'exam'];
if (!in_array($content_type, $allowed_content_types, true)) {
    echo json_encode(['success' => false, 'message' => 'Type de contenu invalide']);
    exit();
}

// Vérification de la propriété : le module doit appartenir au professeur connecté
$stmt = $pdo->prepare("SELECT id FROM assigned_modules WHERE id = ? AND professor_id = ?");
$stmt->execute([$module_id, $_SESSION['user_id']]);
if (!$stmt->fetch()) {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Module non autorisé']);
    exit();
}

$file = $_FILES['file'];

if ($file['error'] !== UPLOAD_ERR_OK) {
    echo json_encode(['success' => false, 'message' => 'Erreur lors du téléversement du fichier']);
    exit();
}

// Limite de taille : 10 Mo
$max_size = 10 * 1024 * 1024;
if ($file['size'] > $max_size) {
    echo json_encode(['success' => false, 'message' => 'Fichier trop volumineux (maximum 10 Mo)']);
    exit();
}

// Extensions autorisées
$allowed_extensions = ['pdf', 'doc', 'docx', 'ppt', 'pptx'];
$file_extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));

if (!in_array($file_extension, $allowed_extensions, true)) {
    echo json_encode(['success' => false, 'message' => 'Type de fichier non autorisé']);
    exit();
}

// Types MIME autorisés (vérification du contenu réel, pas seulement de l'extension)
$allowed_mimes = [
    'application/pdf',
    'application/msword',
    'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
    'application/vnd.ms-powerpoint',
    'application/vnd.openxmlformats-officedocument.presentationml.presentation',
];

$finfo = new finfo(FILEINFO_MIME_TYPE);
$detected_mime = $finfo->file($file['tmp_name']);

if (!in_array($detected_mime, $allowed_mimes, true)) {
    echo json_encode(['success' => false, 'message' => 'Type de fichier non autorisé']);
    exit();
}

// Dossier de téléversement
$upload_dir = __DIR__ . '/uploads';
if (!is_dir($upload_dir)) {
    mkdir($upload_dir, 0755, true);
}

// Nom de fichier sûr et unique (le nom d'origine n'est jamais utilisé)
$new_filename = bin2hex(random_bytes(16)) . '.' . $file_extension;
$upload_path = $upload_dir . '/' . $new_filename;

if (!move_uploaded_file($file['tmp_name'], $upload_path)) {
    echo json_encode(['success' => false, 'message' => 'Échec du téléchargement du fichier']);
    exit();
}

try {
    $stmt = $pdo->prepare("INSERT INTO course_content (module_id, title, description, file_path, content_type, upload_date) VALUES (?, ?, ?, ?, ?, NOW())");
    $stmt->execute([$module_id, $title, $description, 'uploads/' . $new_filename, $content_type]);

    echo json_encode(['success' => true, 'message' => 'Fichier téléchargé avec succès']);
} catch (PDOException $e) {
    // En cas d'erreur base de données, suppression du fichier téléchargé
    error_log('Erreur upload (base de données) : ' . $e->getMessage());
    if (is_file($upload_path)) {
        unlink($upload_path);
    }
    echo json_encode(['success' => false, 'message' => 'Erreur de base de données']);
}
