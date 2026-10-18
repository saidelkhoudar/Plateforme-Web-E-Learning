<?php
/**
 * Endpoint minimal qui renvoie le jeton CSRF de la session courante au format JSON.
 * Permet aux pages statiques (.html) d'obtenir un jeton sans être converties en PHP.
 */
require_once __DIR__ . '/includes/security.php';

header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-store');

echo json_encode(['token' => csrf_token()]);
