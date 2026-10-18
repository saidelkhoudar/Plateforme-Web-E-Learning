<?php
/**
 * Helpers de sécurité partagés (session, CSRF, échappement HTML).
 * Aucun framework requis : fonctions procédurales uniquement.
 */

// Démarre la session une seule fois, de façon sûre.
function session_boot(): void
{
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
}

// Retourne le jeton CSRF de la session courante (le crée si nécessaire).
function csrf_token(): string
{
    session_boot();
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

// Champ caché prêt à insérer dans un formulaire HTML.
function csrf_field(): string
{
    return '<input type="hidden" name="csrf_token" value="'
        . htmlspecialchars(csrf_token(), ENT_QUOTES, 'UTF-8') . '">';
}

// Vérifie un jeton reçu (comparaison à temps constant).
function csrf_verify(?string $token): bool
{
    session_boot();
    return is_string($token)
        && $token !== ''
        && !empty($_SESSION['csrf_token'])
        && hash_equals($_SESSION['csrf_token'], $token);
}

// Échappement HTML de toute donnée non fiable avant affichage.
function e($value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

// La session est démarrée dès l'inclusion de ce fichier, de façon sûre.
session_boot();
