# Copilot Instructions — Zubi Tours

Short, actionable guidance to help AI coding agents be productive in this repo.

1. What this project is
   - A PHP/MySQL monolith for a travel site (public site + admin panel).
   - Frontend pages live under the repository root and `public/` (e.g. `public/packages.php`, `index.php`).
   - Admin UI is under `admin/` (pages in `admin/pages/`) and uses server-side rendered forms and AJAX to `logic/*` endpoints.

2. Quick local setup (manual steps you can follow or automate)
   - Start an Apache+PHP+MySQL stack (XAMPP recommended on Windows).
   - Import the database SQL: `phpmyadmin` or `mysql -u root -p travel_db < "travel_db (1).sql"`.
   - Ensure `upload/` subfolders exist and are writable (e.g. `upload/destinations`, `upload/packages`, `upload/gallery`, `upload/cars`).
   - Configure DB credentials in `admin/includes/connection.php` (the file contains local and commented production values).
   - Use PHP 8+ (SQL dump file indicates development on PHP 8.x). Ensure `mysqlnd` is available (some code uses `get_result()`).

3. Key conventions and patterns (project-specific)
   - Database access: code mostly uses mysqli prepared statements (e.g. `$stmt = $conn->prepare(...); $stmt->bind_param(...);`), but some files incorrectly use PDO-style named params (see `admin/index.php`, `admin/pages/register.php`).
   - Sessions/Authentication:
     - Admin login sets `$_SESSION['admin_logged_in'] = true`, `$_SESSION['admin_id']`, `$_SESSION['admin_name']`.
     - Several admin pages check `if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) { header('Location: ../index.php'); exit(); }`.
   - Flash messages: `$_SESSION['flash_message']` and `$_SESSION['flash_type']` used to show one-time messages in admin pages.
   - JSON storage: complex fields (highlights, activities, tips, itinerary, inclusions/exclusions) are stored as JSON strings in DB and handled as arrays in the UI (server encodes/decodes with `json_encode`/`json_decode`). Example: `highlights`, `activities` in `admin/pages/manage-destinations.php`.
   - Images: files are uploaded to `upload/<type>/` and DB stores relative paths like `destinations/xyz.jpg`. Frontend constructs URLs as `../upload/` or `../../upload/` depending on page location.
   - AJAX endpoints: admin UI calls `../logic/get_destination.php?id=...` and other `logic/*` endpoints expecting JSON responses.

4. Notable issues and things to watch for (important for an agent)
   - DB API mismatch: `admin/includes/connection.php` sets up mysqli connection (`mysqli_connect(...)`) but `admin/index.php` and some register/change password pages use PDO-style `:named` parameters and `PDOException`. This is a source of bugs — either adapt connection to PDO or update those files to mysqli-style prepared statements.
   - Schema drift: `travel_db (1).sql` exists in repo, but some code refers to column names and tables that appear different from that dump (e.g. `destinations` columns in code: `destination_name`, `best_seasons`, etc.). Verify the actual live schema before making DB changes.
   - Security: production DB credentials are present as commented values in `admin/includes/connection.php` — do not commit real secrets. Passwords in `admins` table are bcrypt (use `password_verify`).
   - `get_result()` usage requires `mysqlnd`; confirm CI/dev PHP builds include it.

5. Developer workflows (practical tasks an agent may perform)
   - Add a new admin page or API:
     - Follow the existing style: server-side form in `admin/pages/`, process POST with prepared statements, store files under `upload/`, redirect and set `$_SESSION['flash_message']`.
   - Fix a bug related to DB API mismatch:
     - Search for PDO-style usage (`:name`) and convert to mysqli `?` placeholders and `bind_param`, or change connection to PDO then update other usage accordingly.
   - Reproduce/report issues locally:
     - Start XAMPP, import SQL, ensure `upload/` directories exist, use admin login (see `admins` table in SQL — default user exists in dump).

6. Quick file map (where to start looking)
   - Admin dashboard & pages: `admin/adminpannel.php`, `admin/pages/` (many CRUD pages: `manage-destinations.php`, `manage-packages.php`, `manage-contacts.php`)
   - DB connection: `admin/includes/connection.php` (update credentials here)
   - Public site pages: root (`index.php`) and `public/` (e.g. `public/packages.php`, `public/package-details.php`)
   - Logic / endpoints referenced by admin UI: look for `fetch('../logic/*.php')` calls in admin JS inside `admin/pages/*`.
   - Assets and uploads: `assets/` and `upload/`
   - DB dump: `travel_db (1).sql`

7. PR and code style notes
   - Keep changes minimal and forward-compatible; prefer prepared statements and consistent error handling.
   - When modifying DB schemas, update `travel_db (1).sql` and include a short migration note in the PR description.

8. Questions for maintainers (leave in PR description if unsure)
   - Should the project use mysqli or PDO consistently (pick one)?
   - Which schema is canonical (the SQL dump or the live DB)?
   - Any CI/CD or deployment steps (there are no config files or workflows in repo)?

If anything is unclear or you want me to expand or include a short script to bootstrap a dev environment (import SQL, ensure folders, seed admin user), tell me which area to cover and I'll update this file accordingly.