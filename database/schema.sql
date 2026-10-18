-- ============================================================
-- Schéma de référence — Plateforme « Espace Étudiant »
-- ------------------------------------------------------------
-- IMPORTANT :
--   * Ce script est NON DESTRUCTIF : il n'utilise que
--     CREATE DATABASE/TABLE ... IF NOT EXISTS (aucun DROP / ALTER).
--   * Il ne remplace ni ne modifie aucune donnée existante.
--   * À exécuter de préférence sur une base VIDE ou de test.
--   * Le nom des colonnes est déduit des requêtes de l'application.
-- ============================================================

CREATE DATABASE IF NOT EXISTS `elearning`
    DEFAULT CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;

USE `elearning`;

-- Comptes utilisateurs (étudiants et professeurs)
CREATE TABLE IF NOT EXISTS `users` (
    `id`         INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `email`      VARCHAR(255) NOT NULL,
    `password`   VARCHAR(255) NOT NULL,
    `role`       ENUM('student','professor') NOT NULL DEFAULT 'student',
    `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uniq_users_email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Modules assignés à un professeur
CREATE TABLE IF NOT EXISTS `assigned_modules` (
    `id`           INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `professor_id` INT UNSIGNED NOT NULL,
    `module_code`  VARCHAR(50)  NOT NULL,
    `module_name`  VARCHAR(255) NOT NULL,
    PRIMARY KEY (`id`),
    KEY `idx_am_professor` (`professor_id`),
    CONSTRAINT `fk_am_professor`
        FOREIGN KEY (`professor_id`) REFERENCES `users` (`id`)
        ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Contenus pédagogiques (cours, TD/TP, examens)
CREATE TABLE IF NOT EXISTS `course_content` (
    `id`           INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `module_id`    INT UNSIGNED NOT NULL,
    `title`        VARCHAR(255) NOT NULL,
    `description`  TEXT NULL,
    `file_path`    VARCHAR(255) NOT NULL,
    `content_type` ENUM('course','exercise','exam') NOT NULL,
    `upload_date`  DATETIME NOT NULL,
    PRIMARY KEY (`id`),
    KEY `idx_cc_module` (`module_id`),
    CONSTRAINT `fk_cc_module`
        FOREIGN KEY (`module_id`) REFERENCES `assigned_modules` (`id`)
        ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
