-- ============================================================
-- Exemple — création d'un compte professeur
-- ------------------------------------------------------------
-- La création publique de comptes ne produit QUE des étudiants.
-- Les comptes professeurs sont créés par un administrateur.
--
-- ÉTAPES :
--   1) Générer le hachage du mot de passe (à partir d'un terminal PHP) :
--        php -r "echo password_hash('VotreMotDePasse', PASSWORD_DEFAULT), PHP_EOL;"
--   2) Remplacer les valeurs ci-dessous (e-mail + hachage généré).
--   3) Exécuter ce script sur la base de données.
-- ============================================================

INSERT INTO `users` (`email`, `password`, `role`)
VALUES (
    'professeur@exemple.ma',
    '$2y$10$REMPLACER_PAR_LE_HACHAGE_GENERE_PAR_PHP',
    'professor'
);

-- Exemple — assigner un module à un professeur (adapter l'ID) :
--
-- INSERT INTO `assigned_modules` (`professor_id`, `module_code`, `module_name`)
-- VALUES (2, 'M42', 'Programmation Web 2 : Langage JavaScript');
