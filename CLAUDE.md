# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project

Portail Numérique du Ministère des Transports (RDC) — a plain-PHP admin/citizen portal for registering
drivers (conducteurs), vehicles, taxes, parking, and printing/receiving professional driver cards
("brevets"). No framework (no Laravel/Symfony) — this is a small hand-rolled MVC on top of PDO/PostgreSQL.
UI copy, routes, and DB columns are in French.

## Commands

There is no build step, test suite, or linter configured (`composer.json` only pulls in
`phpoffice/phpspreadsheet` for Excel export). Development loop is: edit PHP → reload in browser.

- Install PHP deps: `composer install`
- Copy `.env.example` → `.env` and fill in real values (DB + SMS credentials — see "Configuration"
  below). `.env` is git-ignored; never commit real secrets into `config/database.php` or
  `app/Core/SmsService.php` again.
- Create the Postgres role/database once (not scripted — do it however fits the environment), e.g.:
  `createdb min_transport` or `psql -c "CREATE DATABASE min_transport OWNER <role>;"`. `database/seed.php`
  does **not** create the database itself (a low-privilege app role usually can't), only the schema inside
  it.
- Initialize/seed the database (creates all tables via `database/schema.sql`, inserts default users):
  `php database/seed.php`. Idempotent — safe to re-run (`IF NOT EXISTS` / `ON CONFLICT DO NOTHING`
  throughout).
- Run locally with PHP's built-in server + the bundled router (replicates `.htaccess`'s rewrite-everything
  behavior so pretty URLs and `$_GET['url']` work without Apache): `php -S localhost:8000 router.php`.
  Apache + `mod_rewrite` (the original `.htaccess`) still works too and is what's expected in production.
- The old `database/migration_*.sql` files are MySQL-era and superseded — their changes are already baked
  into `database/schema.sql`. They're kept only for history; don't run them against Postgres.

## Configuration

Everything credential-shaped is read from environment variables via `App\Core\Env` (`app/Core/Env.php`),
a minimal `.env` parser loaded once in `index.php` before `config/database.php` is read (real environment
variables, e.g. set by Apache/systemd, always take precedence over `.env`). `database/seed.php` loads
`.env` itself since it runs outside `index.php`. See `.env.example` for the full variable list:
`DB_HOST`/`DB_PORT`/`DB_DATABASE`/`DB_USERNAME`/`DB_PASSWORD` for Postgres, `SMS_API_ID`/`SMS_API_PASSWORD`/
`SMS_SENDER_ID`/`SMS_DEBUG` for the Dream Digital SMS API. Neither `config/database.php` nor
`app/Core/SmsService.php` should ever hardcode a real credential again — only non-secret local-dev
fallback defaults belong in code.

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

**`app/Models/User.php`** exists but is barely used (controllers query the DB directly via raw SQL almost
everywhere) — it's now aligned with the real `utilisateurs`/`mot_de_passe` schema, but if you touch it,
double-check callers still expect its shape rather than trusting it's exercised by tests.

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

**Database (`app/Core/Database.php`).** Singleton PDO wrapper (`Database::getInstance()`, `pgsql` driver)
with `query/fetch/fetchOne/fetchAll/insert/update/delete` helpers, all parameterized
(`PDO::ATTR_EMULATE_PREPARES` is off, so real server-side prepares are used — watch for the usual pgsql
gotchas: `LIMIT`/`OFFSET` params need to be actual integers, not decorated strings). `config/database.php`
just reads `DB_*` env vars (see "Configuration"); there is no more environment auto-detection baked into
that file.

**MySQL → PostgreSQL portability.** The app was ported from MySQL; when writing new raw SQL, avoid
MySQL-only syntax that has no Postgres equivalent used elsewhere in the codebase: no `ON DUPLICATE KEY
UPDATE` (use `INSERT ... ON CONFLICT (col) DO UPDATE SET ...`), no `CURDATE()`/`DATE_ADD(..., INTERVAL n
UNIT)` (use `CURRENT_DATE` / `CURRENT_DATE + INTERVAL 'n unit'`), no `DATE_FORMAT()` (use `TO_CHAR()`), no
backtick identifiers, no `GROUP BY` on non-aggregated/non-grouped columns (Postgres always enforces
`ONLY_FULL_GROUP_BY`-style strictness, no way to relax it). Boolean-ish columns (`est_actif`, `est_publie`,
`est_principal`, `travaux_en_cours`) are stored as `SMALLINT` (0/1), not native Postgres `BOOLEAN` —
intentional, since PDO_PGSQL returns booleans as the strings `'t'`/`'f'` (both PHP-truthy), which silently
breaks the codebase's existing `== 1` / truthy checks on fetched rows. Keep new boolean-ish columns as
`SMALLINT` too unless you also update every read site to handle `'t'`/`'f'` explicitly.

**App-level config (not env config)** like the site name/logo/slogan and the brevet card's printed
titles/signature live in a DB table (`configuration`, key/value) rather than a config file, managed via
`ConfigController` (`/admin/config`) and read anywhere with the static helper
`ConfigController::get($key, $default)`.

**Schema (`database/schema.sql`, ~19 tables, Postgres dialect, consolidated/canonical).** Core entities:
`utilisateurs`, `conducteurs`, `vehicules`, `association_conducteur_vehicule`, `cartes_professionnelles`,
`documents`, `taxes`, `paiements_brevets`, `paiements`, `inspections`, `sanctions`, `parkings`,
`stationnement`, `infrastructures`, `signalements`, `articles`, `journal_activites`, `configuration`,
`contact_messages` (the last one didn't exist in the original MySQL schema even though `ContactController`
required it — added here). Foreign keys mostly `ON DELETE SET NULL`. There are no native Postgres `ENUM`
types; MySQL `ENUM` columns became `VARCHAR` + `CHECK (col IN (...))` — those `CHECK` constraints are the
authority for valid values, check them before adding a new role or status rather than assuming the PHP
side is exhaustive. Columns that used MySQL's `... DATETIME ... ON UPDATE CURRENT_TIMESTAMP` (e.g.
`date_modification`) now rely on a `BEFORE UPDATE` trigger (`set_date_modification()`, attached per table)
since Postgres has no equivalent column-level clause — if you add a new `date_modification` column, also
attach that trigger to the table or it will silently stop updating.

**Assets.** Served from `public/assets/{css,js}`; uploaded files (photos, signatures, logos) go under
`public/uploads/{conducteurs,signatures,logos}`. Views reference the `ASSETS_PATH`/`BASE_PATH` constants
defined in `index.php` rather than hardcoded paths, since the app may be deployed in a subdirectory.

**SMS (`app/Core/SmsService.php`).** Sends via the Dream Digital HTTP API using credentials from
`SMS_API_ID`/`SMS_API_PASSWORD`/`SMS_SENDER_ID` env vars (`envoyer()` fails closed and logs if unset,
rather than silently using a real default); phone numbers are normalized to 9-digit RDC national format
before sending. Debug logging (gated on `SMS_DEBUG`) writes to `storage/logs/sms_debug.log`.
