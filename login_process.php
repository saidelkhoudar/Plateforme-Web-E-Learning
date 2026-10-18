<?php
require_once __DIR__ . '/includes/security.php';
require_once __DIR__ . '/config/database.php';

if ($_SERVER["REQUEST_METHOD"] != "POST") {
    header("Location: sign-in.html");
    exit();
}

// Protection CSRF
if (!csrf_verify($_POST['csrf_token'] ?? null)) {
    $_SESSION['error'] = "Session expirée ou requête invalide. Merci de réessayer.";
    header("Location: sign-in.html");
    exit();
}

$email = trim($_POST['email'] ?? '');
$password = $_POST['password'] ?? '';

if ($email === '' || $password === '') {
    $_SESSION['error'] = "Adresse e-mail ou mot de passe incorrect.";
    header("Location: sign-in.html");
    exit();
}

$stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
$stmt->execute([$email]);
$user = $stmt->fetch();

if ($user && password_verify($password, $user['password'])) {
    session_regenerate_id(true);

    $_SESSION['user_id'] = $user['id'];
    $_SESSION['email'] = $user['email'];
    $_SESSION['role'] = $user['role'];

    if ($user['role'] == 'professor') {
        header("Location: professor_dashboard.php");
        exit();
    } else {
        header("Location: student_dashboard.php");
        exit();
    }
}

$_SESSION['error'] = "Adresse e-mail ou mot de passe incorrect.";
header("Location: sign-in.html");
exit();
