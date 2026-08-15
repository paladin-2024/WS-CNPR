# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project

Portail Numérique du Ministère des Transports (RDC) — a plain-PHP admin/citizen portal for registering
drivers (conducteurs), vehicles, taxes, parking, and printing/receiving professional driver cards
("brevets"). No framework (no Laravel/Symfony) — this is a small hand-rolled MVC on top of PDO/MySQL.
UI copy, routes, and DB columns are in French.

## Commands

There is no build step, test suite, or linter configured (`composer.json` only pulls in
`phpoffice/phpspreadsheet` for Excel export). Development loop is: edit PHP → reload in browser.

- Install PHP deps: `composer install`
- Initialize/seed the database (creates DB `min_transport`, runs `database/schema.sql`, inserts default
  users): `php database/seed.php` (can also be hit over HTTP at `/database/seed.php`)
- Apply-once migrations live as standalone `.sql` files in `database/` (`migration_add_roles.sql`,
  `migration_brevets.sql`, `migration_config.sql`) — run manually against the DB when needed; there is no
  migration runner/tracking table.
- Local server: requires Apache + mod_rewrite (XAMPP is what the codebase assumes) because routing
  depends on `.htaccess` rewriting all requests to `index.php?url=...`. PHP's built-in server
  (`php -S`) will not resolve routes without a router script that replicates that rewrite.

## Architecture

**Single front controller.** `index.php` at the repo root does everything an app bootstrap normally
splits up: starts the session, defines path constants (`ROOT_PATH`, `BASE_PATH`, `APP_PATH`,
`ASSETS_PATH`), registers a manual PSR-4-style autoloader for `App\` → `app/` (composer's own autoloader
is only used for vendor packages, not app code), declares every route inline, then dispatches. When
adding a route, add it here — there is no separate routes file.

**Router (`app/Core/Router.php`).** Routes are `[method, path, 'Controller@method', middleware[]]`.
`{param}` segments become named regex captures and are passed positionally to the controller method.
Middleware is just strings interpreted in `dispatch()`: `'auth'` requires `Auth::check()`, and
`'role:admin,agent,...'` requires `Auth::hasRole()` to match one of the comma-separated roles. There's no
generic middleware pipeline/registry — new middleware types must be added as `if` branches in
`dispatch()`.

**Controllers (`app/Controllers/`).** Extend `App\Core\Controller`, which provides `render($view, $data,
$layout = 'main')`, `redirect($url)`, `json($data, $code)`, and `isAjax()`. Controllers talk to the
database directly via `Database::getInstance()` with raw parameterized SQL — there is essentially no
Model layer in practice. `AdminController.php` (~1200 lines) is the largest one and covers dashboard,
conducteurs, véhicules, taxes, paiements, parkings, statistiques and the `/admin/api/*` AJAX endpoints.
`BrevetController.php` handles the imprimeur/réceptionnaire brevet-printing workflow, including
PhpSpreadsheet Excel export and photo/QR-code downloads.

**`app/Models/User.php` is stale/unused** — it queries a `users` table with a `password` column, but the
real schema (and every controller) uses `utilisateurs` with `mot_de_passe`, `nom`, `prenom`, etc. Don't
treat this model as the source of truth for the users table; follow the raw SQL in
`AuthController`/`AdminController` instead.

**Views (`app/Views/`).** Plain PHP templates, one subdirectory per feature (`admin/`, `auth/`, `home/`,
`profile/`, etc.). `Controller::render()` captures the view's output buffer into `$content` and includes
a layout from `app/Views/layouts/`: `main.php` (public site header/footer), `admin.php` (dashboard shell
with role-based sidebar — menu items declare an allowed `roles` array), or `none.php` (bare, for
login/register). Views escape with `htmlspecialchars()` manually; there's no templating engine.

**Auth (`app/Core/Auth.php`).** Session-based (`$_SESSION['user']`), no tokens/JWT. `hasRole()` checks
against the route's `role:` middleware list; `hasPermission()` is a separate, hardcoded
role→permissions map that some feature code checks in addition to/instead of roles. Roles are numerous
and domain-specific: `admin`, `minister_admin`, `agent`, `inspecteur`, `gestionnaire_parking`,
`transporteur`, `conducteur`, `citoyen`, `operateur_saisie`, `validateur`, `receveur`, `instructeur`,
`imprimeur`, `receptionnaire`. When adding an admin feature, decide which of these roles should see it
in both the route middleware and the admin sidebar menu (`app/Views/layouts/admin.php`).

**Database (`app/Core/Database.php`).** Singleton PDO wrapper (`Database::getInstance()`) with
`query/fetch/fetchOne/fetchAll/insert/update/delete` helpers, all parameterized. `config/database.php`
auto-detects local vs. production credentials by checking `APP_ENV`/`USE_LOCAL_DB` env vars, then falling
back to host/IP heuristics (localhost/127.0.0.1 → local XAMPP config, anything else → production). Both
credential sets, including the production DB password, are committed in plaintext in that file — be
aware of this when touching it, and don't add new real secrets to source instead of environment config.

**App-level config (not env config)** like the site name/logo/slogan and the brevet card's printed
titles/signature live in a DB table (`configuration`, key/value) rather than a config file, managed via
`ConfigController` (`/admin/config`) and read anywhere with the static helper
`ConfigController::get($key, $default)`.

**Schema (`database/schema.sql`, ~17 tables).** Core entities: `utilisateurs`, `conducteurs`,
`vehicules`, `association_conducteur_vehicule`, `cartes_professionnelles`, `documents`, `taxes`,
`paiements_brevets`, `paiements`, `inspections`, `sanctions`, `parkings`, `stationnement`,
`infrastructures`, `signalements`, `articles`, `journal_activites`, `configuration`. Foreign keys mostly
`ON DELETE SET NULL`. Enum columns (e.g. `utilisateurs.role`, `conducteurs.statut_brevet`) are the
authority for valid values — check them before adding a new role or status rather than assuming the PHP
side is exhaustive.

**Assets.** Served from `public/assets/{css,js}`; uploaded files (photos, signatures, logos) go under
`public/uploads/{conducteurs,signatures,logos}`. Views reference the `ASSETS_PATH`/`BASE_PATH` constants
defined in `index.php` rather than hardcoded paths, since the app may be deployed in a subdirectory.

**SMS (`app/Core/SmsService.php`).** Sends via the Dream Digital HTTP API using hardcoded credentials in
the class constants; phone numbers are normalized to 9-digit RDC national format before sending. Debug
logging (`DEBUG = true`) writes to `storage/logs/sms_debug.log`.
