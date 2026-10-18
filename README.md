# Espace Étudiant — University E-learning Platform

A lightweight PHP/MySQL e-learning platform for the **Licence Fondamentale "Ingénierie des
Systèmes d'Information" (ISI)** at the *Faculté Polydisciplinaire de Taza, Université Sidi
Mohamed Ben Abdallah* (Morocco).

Students browse and download course materials (courses, TD/TP, exams); professors manage the
content for the modules assigned to them. Public pages present the university and the program.

The user interface is in French.

## Tech stack

| Layer     | Technology                                                        |
|-----------|-------------------------------------------------------------------|
| Backend   | PHP (procedural, PDO), sessions                                   |
| Database  | MySQL / MariaDB                                                   |
| Frontend  | HTML5, Bootstrap 5, Bootstrap Icons, Slick, Headroom, jQuery      |
| Tooling   | None required — no Composer, no npm, no build step                |

## Requirements

- PHP 7.4+ (tested with PHP 8.3)
- MySQL 5.7+ / MariaDB 10.3+
- Apache (XAMPP / WAMP / Laragon) or PHP's built-in server

## Setup

1. **Get the code** into your web root (e.g. `htdocs/elearning`).
2. **Create the database schema** (non-destructive, `CREATE ... IF NOT EXISTS` only):
   ```bash
   mysql -u root -p < database/schema.sql
   ```
   Use a fresh/test database first — the script never drops or alters existing tables.
3. **Configure the connection**:
   ```bash
   cp config/config.example.php config/config.php
   ```
   Then edit `config/config.php` with your database credentials.
   `config/config.php` is git-ignored and must never be committed.
4. **Make the uploads folder writable** by the web server user.
5. **Run** the app by opening `index.html` in the browser (or `php -S localhost:8000` for local
   development).

## Creating accounts

- **Students** register through `sign-up.html`; public registration always creates a
  `student` account.
- **Professors** are created by an administrator. See [database/seed.example.sql](database/seed.example.sql)
  for the documented procedure (generate a password hash with PHP, then insert the row).

## Project structure

```
config/            Database config (config.example.php committed, config.php ignored)
database/          schema.sql, seed.example.sql
includes/          Shared security helpers (session, CSRF, HTML escaping)
uploads/           User-uploaded materials (git-ignored)
css/ js/ images/   Front-end assets
*.php              Auth + dashboards + upload/delete endpoints
*.html             Public static pages
```

## Security notes (Phase 1)

- Database credentials are loaded from an untracked config file.
- Passwords are stored with `password_hash()` and verified with `password_verify()`.
- State-changing requests (upload, delete) require a CSRF token and verify that the target
  module belongs to the logged-in professor.
- Untrusted output is escaped before rendering.
- Uploads are validated by extension, MIME type and size.

## Known limitations / roadmap

- The student dashboard currently covers **Semester 1** only (S2–S6 pending).
- The public contact form has no backend yet.
- Google sign-in is a placeholder.
- No automated tests yet.

## License

Not yet specified.
