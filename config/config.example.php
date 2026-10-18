<?php
/**
 * Modèle de configuration de la base de données.
 *
 * Utilisation :
 *   1. Copiez ce fichier en `config/config.php`.
 *   2. Renseignez vos propres identifiants.
 *
 * `config/config.php` est ignoré par Git : n'y placez jamais de secrets
 * destinés à être publiés sur GitHub.
 */
return [
    'host'     => 'localhost',
    'port'     => 3306,
    'dbname'   => 'elearning',
    'username' => 'VOTRE_UTILISATEUR',
    'password' => 'VOTRE_MOT_DE_PASSE',
    'charset'  => 'utf8mb4',
];
