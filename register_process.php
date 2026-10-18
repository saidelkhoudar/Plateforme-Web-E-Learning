<?php
require_once __DIR__ . '/includes/security.php';
require_once __DIR__ . '/config/database.php';

if ($_SERVER["REQUEST_METHOD"] != "POST") {
    header("Location: sign-up.html");
    exit();
}

// Protection CSRF
if (!csrf_verify($_POST['csrf_token'] ?? null)) {
    $_SESSION['error'] = "Session expirée ou requête invalide. Merci de réessayer.";
    header("Location: sign-up.html");
    exit();
}

$email = trim($_POST['email'] ?? '');
$password = $_POST['password'] ?? '';
$confirm = $_POST['confirm_password'] ?? '';

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $_SESSION['error'] = "Adresse e-mail invalide.";
    header("Location: sign-up.html");
    exit();
}

if ($password === '') {
    $_SESSION['error'] = "Le mot de passe est obligatoire.";
    header("Location: sign-up.html");
    exit();
}

// Validation de la confirmation du mot de passe
if ($password !== $confirm) {
    $_SESSION['error'] = "Les deux mots de passe ne correspondent pas.";
    header("Location: sign-up.html");
    exit();
}

// Le rôle est imposé côté serveur : l'inscription publique ne crée que des étudiants.
// La valeur postée par le client (y compris "professor") est volontairement ignorée.
$role = 'student';

try {
    $stmt = $pdo->prepare("INSERT INTO users (email, password, role) VALUES (?, ?, ?)");
    $stmt->execute([$email, password_hash($password, PASSWORD_DEFAULT), $role]);

    $_SESSION['success'] = "Inscription réussie ! Vous pouvez maintenant vous connecter.";
    header("Location: sign-in.html");
    exit();
} catch (PDOException $e) {
    // Le détail reste dans les logs serveur ; le message public n'expose rien.
    error_log('Échec de l\'inscription : ' . $e->getMessage());
    $_SESSION['error'] = "Impossible de créer le compte (adresse e-mail peut-être déjà utilisée).";
    header("Location: sign-up.html");
    exit();
}
