<?php
require_once __DIR__ . '/includes/security.php';
require_once __DIR__ . '/config/database.php';

header('Content-Type: application/json; charset=utf-8');

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'professor') {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Non autorisé']);
    exit();
}

$data = json_decode(file_get_contents('php://input'), true);
if (!is_array($data)) {
    $data = [];
}

// Protection CSRF
if (!csrf_verify($data['csrf_token'] ?? null)) {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Jeton CSRF invalide']);
    exit();
}

$content_id = $data['content_id'] ?? null;

if (!$content_id) {
    echo json_encode(['success' => false, 'message' => 'ID du contenu manquant']);
    exit();
}

try {
    // Vérifier que le professeur est autorisé à supprimer ce contenu
    $stmt = $pdo->prepare("SELECT cc.*, am.professor_id 
                          FROM course_content cc 
                          JOIN assigned_modules am ON cc.module_id = am.id 
                          WHERE cc.id = ? AND am.professor_id = ?");
    $stmt->execute([$content_id, $_SESSION['user_id']]);
    $content = $stmt->fetch();

    if (!$content) {
        echo json_encode(['success' => false, 'message' => 'Contenu non trouvé ou non autorisé']);
        exit();
    }

    // Supprimer le fichier physique (en restant confiné au dossier uploads/)
    $uploads_dir = realpath(__DIR__ . '/uploads');
    $full_path = realpath(__DIR__ . '/' . ltrim(str_replace('\\', '/', $content['file_path']), '/'));

    if ($uploads_dir && $full_path && strpos($full_path, $uploads_dir) === 0 && is_file($full_path)) {
        unlink($full_path);
    }

    // Supprimer l'enregistrement de la base de données
    $stmt = $pdo->prepare("DELETE FROM course_content WHERE id = ?");
    $stmt->execute([$content_id]);

    echo json_encode(['success' => true]);
} catch (PDOException $e) {
    error_log('Erreur suppression (base de données) : ' . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Erreur de base de données']);
}
