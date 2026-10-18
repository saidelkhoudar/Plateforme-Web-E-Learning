<?php
/**
 * Connexion PDO à la base de données.
 * Les identifiants sont chargés depuis `config/config.php` (non versionné).
 * Voir `config/config.example.php` pour le modèle à copier.
 */

$configFile = __DIR__ . '/config.php';

if (!is_file($configFile)) {
    error_log('Configuration manquante : config/config.php absent.');
    http_response_code(500);
    exit('Configuration de la base de données manquante. Copiez config/config.example.php vers config/config.php.');
}

$config = require $configFile;

$host    = $config['host'] ?? 'localhost';
$port    = $config['port'] ?? 3306;
$dbname  = $config['dbname'] ?? 'elearning';
$charset = $config['charset'] ?? 'utf8mb4';

try {
    $pdo = new PDO(
        "mysql:host={$host};port={$port};dbname={$dbname};charset={$charset}",
        $config['username'] ?? '',
        $config['password'] ?? ''
    );
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    // Détail écrit dans les logs serveur uniquement (jamais affiché au navigateur).
    error_log('Échec de connexion à la base de données : ' . $e->getMessage());
    http_response_code(500);
    exit('Connexion à la base de données impossible.');
}
