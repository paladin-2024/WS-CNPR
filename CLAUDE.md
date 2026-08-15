# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project

Portail Numérique du Ministère des Transports (RDC) — a plain-PHP admin/citizen portal for registering
drivers (conducteurs), vehicles, taxes, parking, and printing/receiving professional driver cards
("brevets"). No framework (no Laravel/Symfony) — this is a small hand-rolled MVC on top of PDO/PostgreSQL.
UI copy, routes, and DB columns are in French.

## Commands

There is no test suite or linter configured. Composer is used for one runtime dependency
(`phpoffice/phpspreadsheet`, Excel export) and its own autoloader (`autoload.psr-4: App\ -> app/`).
Development loop is: edit PHP → reload in browser.

- Install PHP deps: `composer install` (add `--no-dev --optimize-autoloader` in production).
- Copy `.env.example` → `.env` and fill in real values (DB + SMS credentials — see "Configuration"
  below). `.env` is git-ignored; never commit real secrets into `config/database.php` or
  `app/Core/SmsService.php` again.
- Create the Postgres role/database once (not scripted — do it however fits the environment), e.g.:
  `createdb min_transport` or `psql -c "CREATE DATABASE min_transport OWNER <role>;"`. `database/seed.php`
  does **not** create the database itself (a low-privilege app role usually can't), only the schema inside
  it.
- Initialize/seed the database (creates all tables via `database/schema.sql`, inserts default users):
  `php database/seed.php`. Idempotent — safe to re-run (`IF NOT EXISTS` / `ON CONFLICT DO NOTHING`
  throughout). CLI-only — refuses to run over HTTP (`php_sapi_name() !== 'cli'` guard), same for
  `database/_hash_once.php`.
- Run locally with PHP's built-in server + the bundled router (replicates `.htaccess`'s rewrite-everything
  behavior so pretty URLs and `$_GET['url']` work without Apache): `php -S localhost:8000 router.php`.
- The old `database/migration_*.sql` files are MySQL-era and superseded — their changes are already baked
  into `database/schema.sql`. They're kept only for history; don't run them against Postgres.
- **Deployment**: see `DEPLOYMENT.md` for the full runbook (both a Docker path and a bare-metal
  Nginx+PHP-FPM path). Production runs via Docker (`Dockerfile`, `docker-compose.yml`) on a VPS that also
  hosts other, unrelated sites behind a shared system-level Nginx — this stack's own `nginx` service binds
  `127.0.0.1:8081` only (not 80/443 directly) and the system Nginx reverse-proxies the real domain to it,
  terminating TLS itself via its existing certbot setup. `nginx/conf.d/cnpr.wscsarl.info.conf` is that
  internal-only container config; `deploy/nginx.conf.example` is the alternative for a VPS dedicated only
  to this app (owns 80/443 directly) — don't run both approaches against the same domain at once.

## Configuration

Everything credential-shaped is read from environment variables via `App\Core\Env` (`app/Core/Env.php`),
a minimal `.env` parser loaded once in `index.php` before `config/database.php` is read (real environment
variables, e.g. set by Apache/systemd/Docker, always take precedence over `.env`). `database/seed.php`
loads `.env` itself since it runs outside `index.php`. See `.env.example` for the full variable list:
`DB_HOST`/`DB_PORT`/`DB_DATABASE`/`DB_USERNAME`/`DB_PASSWORD` for Postgres, `SMS_API_TOKEN`/`SMS_SENDER_ID`/
`SMS_DEBUG` for the Africala SMS API. Neither `config/database.php` nor `app/Core/SmsService.php` should
ever hardcode a real credential again — only non-secret local-dev fallback defaults belong in code.
`APP_ENV=production` also flips `index.php`'s behavior: suppresses `display_errors` (logs to
`storage/logs/php-error.log` instead) and allows the session cookie's `secure` flag to be set (only when
HTTPS is actually detected, directly or via `X-Forwarded-Proto` from a reverse proxy).

## Architecture

**Single front controller.** `index.php` at the repo root does everything an app bootstrap normally
splits up: loads composer's autoloader, loads `.env`, decides error-display/session-cookie behavior from
`APP_ENV`, starts the session, defines path constants (`ROOT_PATH`, `BASE_PATH`, `APP_PATH`,
`ASSETS_PATH`), declares every route inline, then dispatches. When adding a route, add it here — there is
no separate routes file. Class autoloading is composer's own PSR-4 (`App\` → `app/`, declared in
`composer.json`), not a hand-rolled loader.

**Router (`app/Core/Router.php`).** Routes are `[method, path, 'Controller@method', middleware[]]`.
`{param}` segments become named regex captures and are passed positionally to the controller method.
Every POST/PUT/DELETE request is CSRF-checked centrally in `dispatch()` (see "CSRF" below) before route
middleware runs. Middleware itself is just strings interpreted in `dispatch()`: `'auth'` requires
`Auth::check()`, and `'role:admin,agent,...'` requires `Auth::hasRole()` to match one of the
comma-separated roles. There's no generic middleware pipeline/registry — new middleware types must be
added as `if` branches in `dispatch()`.

**CSRF (`app/Core/Csrf.php`).** Session-bound token, verified for every POST/PUT/DELETE route in
`Router::dispatch()` — reads the token from a `_csrf` form field, an `X-CSRF-Token` header, or a JSON
body's `_csrf` key (covers native form posts and the admin AJAX endpoints, including PUT/DELETE which have
no `$_POST`). Failure responds 419 (JSON for `/api/` routes, flash+redirect otherwise). Every `<form>`
needs `<?= \App\Core\Csrf::field() ?>`; every `fetch()` call needs an `X-CSRF-Token` header — the layouts
expose the current token via `<meta name="csrf-token">` and a `CSRF_TOKEN` JS constant in
`public/assets/js/app.js`. Forms that build their AJAX body via `new FormData(form)` pick up the hidden
field automatically; forms/buttons that construct the body manually (plain objects, `FormData()` with
manual `.append()`) need the header added explicitly — check how an existing similar call does it before
adding a new one.

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

**Assets.** Served from `public/assets/{css,js,icons}`; uploaded files (photos, signatures, logos) go under
`public/uploads/{conducteurs,signatures,logos}`. Views reference the `ASSETS_PATH`/`BASE_PATH` constants
defined in `index.php` rather than hardcoded paths, since the app may be deployed in a subdirectory.

**PWA.** `public/manifest.json` + `public/sw.js`, wired into all 3 layouts (`<link rel="manifest">`,
theme-color meta, service-worker registration). The service worker deliberately caches almost nothing —
cache-first only for static files under `/public/assets/`, network-only for every navigation and all
`/admin/*`/`/admin/api/*` requests. Don't widen that caching scope without a good reason; a stale-served
admin dashboard or driver list would be actively harmful for this app.

**SMS (`app/Core/SmsService.php`).** Sends via Africala's SendSmsV2 API (`api2.smsala.com`) using a single
`SMS_API_TOKEN` (+ `SMS_SENDER_ID`) env var — `envoyer()` fails closed and logs if unset, rather than
silently using a real default. Phone numbers are normalized to 9-digit RDC national format before sending.
Two things about this specific API that aren't obvious from a first read: it deserializes the request body
as a JSON *array* of message objects, not a single object (send a batch of one: `[[...]]`), and it
responds the same way (an array of per-message results) — both the request-building and response-parsing
code account for this. `envoyer()` retries once, but only on a genuine network error or a 5xx from the
provider — never on an API-level rejection (bad token, IP not whitelisted, etc.), since that fails
identically every time. Failures are always logged (`error_log` + `storage/logs/sms_debug.log`); verbose
per-attempt tracing (full request/response bodies) stays gated behind `SMS_DEBUG` to avoid logging message
content by default in production.
