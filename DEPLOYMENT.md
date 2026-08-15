# Deployment Guide — Portail Numérique du Ministère des Transports

This is a hand-rolled PHP MVC app (no framework), a single front controller
(`index.php`) at the repo root, PDO/PostgreSQL for data, and one external
dependency at runtime (the Africala SMS API). There is no build step and
no migration framework — deploying means: get the PHP code + `vendor/` onto a
server, point a web server at it, have a Postgres database with the schema
applied, and set the right environment variables.

This guide assumes the production-hardening work is merged into `dev`
(CSRF protection, `APP_ENV`-driven error/session handling, the CLI-only guard
on `database/seed.php`/`database/_hash_once.php`, the `/register` page,
PWA support, and `deploy/nginx.conf.example` + `deploy/php-fpm-pool.conf.example`),
along with Docker support (`Dockerfile`, `docker-compose.yml`,
`.dockerignore`, `.env.docker.example`). Two deployment paths are documented
below; pick one per environment.

---

## Path A: Docker (recommended)

Uses the `Dockerfile` (multi-stage: `composer:2` vendor stage → `php:8.3-fpm`
runtime with `pdo_pgsql`, `gd`, `zip`, `mbstring`, `curl`, `opcache`) and
`docker-compose.yml` (three services: `nginx` → `app` (PHP-FPM) → `db`
(`postgres:16-alpine`)).

### 1. Prerequisites on the VPS

- Docker Engine + the Docker Compose plugin (`docker compose version` works).
- A domain pointed at the VPS if you want TLS (see step 6).

### 2. Clone and configure

```bash
git clone <repo-url> portail-transport
cd portail-transport
cp .env.docker.example .env
```

Edit `.env` and fill in real values. At minimum:

- `DB_PASSWORD` — required, no default. Leave `DB_HOST=db` and `DB_PORT=5432`
  as-is; these are the Compose service name and the Postgres container's
  *internal* port, not a host-facing address — `docker-compose.yml` also uses
  this same `.env` for `${DB_DATABASE}`/`${DB_USERNAME}`/`${DB_PASSWORD}` to
  configure the `db` service itself, so there's a single source of truth.
- `SMS_API_TOKEN`, `SMS_SENDER_ID` — Africala SMS API credentials
  (`app/Core/SmsService.php`). Leaving `SMS_API_TOKEN` blank is a valid
  choice for launch: `SmsService::envoyer()`
  fails closed (logs and returns `false`) rather than silently sending through
  a placeholder account — SMS just won't go out.
- `APP_ENV=production` — already set in `.env.docker.example`; keep it.
- `SMS_DEBUG=false` — keep `false` in production (SMS debug logging writes
  every attempt to `storage/logs/sms_debug.log`).

### 3. Build and start the stack

```bash
docker compose up -d --build
```

This builds the `app` image, starts `db` (waits for its healthcheck), then
`app`, then `nginx` (bound to host port 80 only, by default — see step 6 for
443).

### 4. One-time schema initialization

The `db` service's `POSTGRES_DB` only creates an *empty* database — the
app's own tables and default accounts still need `database/schema.sql`
applied:

```bash
docker compose exec app php database/seed.php
```

This is idempotent (`IF NOT EXISTS` / `ON CONFLICT DO NOTHING` throughout),
so it's safe to re-run after a `git pull` + rebuild if `schema.sql` changed;
it isn't required on every redeploy, only the first time and after schema
changes.

### 5. Verify

Visit `http://<host>/` (or your domain). `seed.php` prints the accounts it
just inserted — read that output (or `database/seed.php` itself) rather than
trusting memory, but as of this codebase it creates exactly two demo
accounts:

| Email | Password | Role |
|---|---|---|
| `admin@mintransport.gov` | `admin123` | `admin` |
| `agent@mintransport.gov` | `agent123` | `agent` |

**These are known, hardcoded demo credentials with full admin access to a
government transport portal — change both passwords (via `/admin/utilisateurs`
once logged in, or a direct `UPDATE utilisateurs SET mot_de_passe = ...` with
a freshly bcrypt-hashed value) before the deployment is reachable by anyone
outside the deploy operator.** See the pre-launch checklist below — this is
not optional.

### 6. TLS

This app's actual production VPS also hosts other sites (`wscsarl.info`,
`dgpspt.wscsarl.info`) behind a **system-level Nginx that already owns host
ports 80/443** — running this stack's own `nginx` there directly would just
fail to bind the port. So on a co-hosted box, TLS terminates at the system
Nginx, not inside this stack:

- `docker-compose.yml`'s `nginx` service binds `127.0.0.1:8081:80` only —
  plain HTTP, not reachable from outside the VPS.
- Add a new system-Nginx server block (e.g.
  `/etc/nginx/conf.d/cnpr.conf`) that reverse-proxies the real domain to
  `127.0.0.1:8081`, mirroring whatever pattern the box's other sites already
  use:
  ```nginx
  server {
      listen 80;
      server_name cnpr.wscsarl.info;

      location / {
          proxy_pass http://127.0.0.1:8081;
          proxy_set_header Host $host;
          proxy_set_header X-Real-IP $remote_addr;
          proxy_set_header X-Forwarded-For $proxy_add_x_forwarded_for;
          proxy_set_header X-Forwarded-Proto $scheme;
      }
  }
  ```
  Then `sudo nginx -t && sudo systemctl reload nginx`, and
  `sudo certbot --nginx -d cnpr.wscsarl.info` — certbot rewrites this file
  in place to add the HTTPS block/redirect and start managing renewal,
  exactly like it already does for the box's other domains.
- `nginx/conf.d/cnpr.wscsarl.info.conf` (this stack's own, internal-only
  Nginx config) explicitly re-forwards `X-Forwarded-Proto`/`X-Forwarded-For`
  into PHP via `fastcgi_param` — the stock `fastcgi_params` file nginx ships
  with does **not** pass through arbitrary incoming headers on its own, and
  `index.php` reads `HTTP_X_FORWARDED_PROTO` to decide whether to mark the
  session cookie `secure`. Without that explicit forwarding, PHP would never
  see it and the cookie would never get `secure` even over real HTTPS.

If this app ever runs on a VPS **dedicated only to it** (no other sites),
the simpler path is to let this stack's own `nginx` own 80/443 directly
instead — see `deploy/nginx.conf.example` for that variant, and swap the
`ports:`/TLS setup back accordingly. Don't run both approaches on the same
domain at once (system Nginx trying to obtain a cert while a container also
tries to bind 80/443 will fight each other).

### Redeploying

```bash
git pull
docker compose up -d --build
```

`vendor/` is rebuilt inside the image every time (the `vendor` build stage
runs `composer install --no-dev --optimize-autoloader`), so there's no
separate `composer install` step to remember. Only re-run
`docker compose exec app php database/seed.php` if `database/schema.sql`
changed in the pulled commits.

---

## Path B: Bare-metal (Nginx + PHP-FPM + PostgreSQL installed directly)

### 1. Prerequisites

- **PHP ≥ 8.1** (`composer.json`'s `require.php` constraint, driven by
  `phpoffice/phpspreadsheet ^5.5`). The Docker image uses 8.3; match that or
  newer if you have a choice.
- PHP extensions: `pdo`, `pdo_pgsql` (both explicit `composer.json`
  requirements — `app/Core/Database.php` connects via PDO's `pgsql` driver),
  `curl` (`app/Core/SmsService.php` calls the SMS API over cURL), `gd`,
  `zip`, `mbstring` (required by `phpoffice/phpspreadsheet` for
  `BrevetController`'s Excel export). `ctype`, `dom`, `fileinfo`, `filter`,
  `iconv`, `libxml`, `simplexml`, `xml`, `xmlreader`, `xmlwriter`, `zlib` are
  also PhpSpreadsheet dependencies but ship enabled by default in most
  distro PHP packages — verify with `php -m` rather than assuming.
- PostgreSQL server (any recent version — nothing in `database/schema.sql`
  requires a specific minimum).
- Composer.
- Nginx.

On Debian/Ubuntu, something like:

```bash
sudo apt install php8.3-fpm php8.3-pgsql php8.3-curl php8.3-gd php8.3-zip php8.3-mbstring php8.3-xml
```

(package names/versions vary by distro and PHP repo — confirm actual
availability rather than copy-pasting blind).

### 2. Clone and install PHP dependencies

```bash
git clone <repo-url> /var/www/portail-transport
cd /var/www/portail-transport
composer install --no-dev --optimize-autoloader
```

`composer.json`'s `autoload.psr-4` (`App\` → `app/`) plus
`--optimize-autoloader` produces a classmap-authoritative autoloader —
`index.php` requires `vendor/autoload.php` directly, there's no separate
custom autoloader to worry about.

### 3. Create the Postgres role and database

**The app does not create its own database.** `database/seed.php` only
creates *tables inside an existing database* (`schema.sql`, which is
`IF NOT EXISTS`-guarded) — the database itself, and the role that owns it,
must already exist first:

```bash
sudo -u postgres psql -c "CREATE ROLE transport_app WITH LOGIN PASSWORD 'a-real-password';"
sudo -u postgres psql -c "CREATE DATABASE min_transport OWNER transport_app;"
```

(role/database names above match `.env.example`'s defaults —
`DB_USERNAME=transport_app`, `DB_DATABASE=min_transport` — but any name
works as long as `.env` matches). Running `php database/seed.php` against a
database that doesn't exist yet fails with a PDO connection error; this is
the most common first-deploy mistake.

### 4. Configure environment

```bash
cp .env.example .env
```

Fill in real values — see `.env.example` for the authoritative variable
list: `APP_ENV` (`production`), `DB_HOST`/`DB_PORT`/`DB_DATABASE`/
`DB_USERNAME`/`DB_PASSWORD`, `SMS_API_TOKEN`/`SMS_SENDER_ID`/
`SMS_DEBUG`. `.env` is git-ignored; never put real credentials into
`config/database.php` or `app/Core/SmsService.php` — both only read from
`App\Core\Env`, with non-secret local-dev fallbacks baked in as defaults
(see "Configuration notes" below).

### 5. Initialize the schema

```bash
php database/seed.php
```

Applies `database/schema.sql` and inserts the two default accounts (see the
table in Path A step 5 — same script, same accounts, whichever path you
deploy with). Idempotent, safe to re-run after a `git pull` that changed
`schema.sql`. This script (and `database/_hash_once.php`) both check
`php_sapi_name() !== 'cli'` and refuse to run outside a terminal — but
that's a defense-in-depth backstop, not a substitute for the Nginx `deny`
rules in step 6; don't rely on the guard alone to keep them off the public
internet.

### 6. Nginx

```bash
sudo cp deploy/nginx.conf.example /etc/nginx/sites-available/portail-transport.conf
sudo $EDITOR /etc/nginx/sites-available/portail-transport.conf
```

Replace every `CHANGE_ME*` placeholder:

- `CHANGE_ME-fpm.sock` (two places — the `upstream` block's `server unix:` line)
- `CHANGE_ME_DOMAIN` (both the port-80 and, once you uncomment it, port-443
  server blocks)
- `CHANGE_ME_project_root` (the ACME-challenge `root` and, once uncommented,
  the app's `root` directive — this must be the directory containing
  `index.php`, i.e. the repo root, **not** `public/`: `index.php` lives at
  the project root, which is why the config also has explicit `deny` blocks
  for `.env`, `.git`, `vendor/`, `storage/`, `database/`, `app/`, `config/`,
  `composer.json`/`composer.lock` — nothing else stops Nginx from serving
  those as static files otherwise)

Leave the `443` server block commented out for now — the port-80 block's
`/.well-known/acme-challenge/` location is enough for certbot's webroot
validation to work without it. Then enable and check:

```bash
sudo ln -s /etc/nginx/sites-available/portail-transport.conf \
           /etc/nginx/sites-enabled/portail-transport.conf
sudo nginx -t
sudo systemctl reload nginx
```

### 7. PHP-FPM pool

Merge `deploy/php-fpm-pool.conf.example`'s directives into an actual pool
file (typically `/etc/php/<version>/fpm/pool.d/portail-transport.conf` on
Debian/Ubuntu — confirm the real path for the installed PHP version):

```bash
sudo cp deploy/php-fpm-pool.conf.example /etc/php/8.3/fpm/pool.d/portail-transport.conf
sudo $EDITOR /etc/php/8.3/fpm/pool.d/portail-transport.conf
```

Replace `CHANGE_ME_php_fpm_user` / `CHANGE_ME_php_fpm_group` (usually
`www-data`) and the socket path (must match the `upstream` block's socket
path in the Nginx config from step 6 — the two files are meant to be edited
together). This pool file also sets `upload_max_filesize=10M` /
`post_max_size=12M` (must stay ≥ Nginx's `client_max_body_size`, also 10M in
the example — mismatched limits cause uploads to fail differently depending
on which layer rejects first), `memory_limit=256M` and
`max_execution_time=300` (sized for `BrevetController`'s unbounded
Excel/photo/QR-code export endpoints, one of which makes a serial outbound
HTTP call per driver record), and `display_errors = Off` /
`expose_php = Off` as a production backstop independent of `index.php`'s own
`APP_ENV` check.

```bash
sudo php-fpm8.3 -t
sudo systemctl restart php8.3-fpm
```

### 8. TLS via certbot

```bash
sudo certbot certonly --webroot -w /var/www/portail-transport -d your-domain.example
```

Then uncomment the `443` server block in
`/etc/nginx/sites-available/portail-transport.conf`, `sudo nginx -t`, and
`sudo systemctl reload nginx`. Set up certbot's renewal timer (usually
already installed as a systemd timer/cron job by the certbot package) to
also reload Nginx on renewal.

### 9. File permissions

The web server / PHP-FPM user (`www-data` or whatever you set in the pool
file) needs write access to:

- `storage/logs/` — SMS debug log (`sms_debug.log`, gated on `SMS_DEBUG`)
  and, in production, PHP's own error log (`php-error.log`, since
  `index.php` redirects `error_log` there when `APP_ENV=production`).
- `public/uploads/conducteurs/`, `public/uploads/signatures/`,
  `public/uploads/logos/` — driver photos/ID scans, signature images, and
  site logo uploads (`AdminController`, `ConfigController`,
  `VerificationController`).

```bash
sudo chown -R www-data:www-data storage/logs public/uploads
sudo chmod -R 775 storage/logs public/uploads
```

---

## Configuration notes (both paths)

- **`APP_ENV`** drives two things directly in `index.php`: whether PHP
  errors are displayed to the browser (`production` suppresses
  `display_errors` and logs to `storage/logs/php-error.log` instead;
  anything else shows errors inline, which you do **not** want live) and
  the session cookie's `secure` flag (set whenever the request is detected
  as HTTPS — either `$_SERVER['HTTPS']` directly, or
  `X-Forwarded-Proto: https` from a reverse proxy in front of PHP-FPM).
  Session cookies also always get `httponly` and `samesite=Lax` regardless
  of `APP_ENV`.
- **`config/database.php`**'s fallback values (`127.0.0.1` / `min_transport`
  / `postgres` / empty password) are local-dev conventions only, read
  through `App\Core\Env::get($key, $default)` — they only take effect if the
  corresponding `.env` variable (or a real environment variable, which
  always wins over `.env`) is missing. Production `.env` must set all of
  `DB_HOST`/`DB_PORT`/`DB_DATABASE`/`DB_USERNAME`/`DB_PASSWORD` explicitly.

---

## Pre-launch checklist

- [ ] `APP_ENV=production` is actually set in the deployed `.env` (Docker:
      `.env.docker.example` defaults to it; bare-metal: `.env.example`
      defaults to `local` — this must be changed). Confirm by checking that
      a deliberately broken request doesn't leak a PHP stack trace, and that
      `storage/logs/php-error.log` is being written to instead.
- [ ] SMS credentials are either real (`SMS_API_TOKEN` set to an actual
      Africala account value) or the decision to launch
      without SMS is explicit and documented — `SmsService::envoyer()` fails
      closed and logs to `storage/logs/sms_debug.log` (if `SMS_DEBUG=true`)
      rather than erroring loudly, so a missing SMS feature can otherwise go
      unnoticed until someone asks why a driver never got a text.
- [ ] Default seeded account passwords changed: `admin@mintransport.gov`
      (`admin123`) and `agent@mintransport.gov` (`agent123`) — both plain,
      documented, guessable passwords on accounts with real write access to
      citizen/driver records. Change via `/admin/utilisateurs` once logged
      in, or re-hash directly in Postgres.
- [ ] Database credentials in the live `.env` are not `config/database.php`'s
      local-dev fallbacks (`postgres` user, empty password, `127.0.0.1`).
- [ ] `.env` is not web-accessible. Verify against the live domain:
      `curl -I https://your-domain.example/.env` should return `403`/`404`,
      never `200` with file contents.
- [ ] `database/seed.php` and `database/_hash_once.php` are not reachable
      over HTTP — both should already be blocked by Nginx's `deny` on
      `/database/` *and* by their own CLI-only guard
      (`php_sapi_name() !== 'cli'`, HTTP 403), but verify both layers with a
      real request post-deploy:
      `curl -I https://your-domain.example/database/seed.php`.
- [ ] HTTPS is actually enforced: `curl -I http://your-domain.example/`
      should return a `301` to `https://`.
- [ ] Upload directories exist and are writable by the PHP-FPM/web server
      user: `public/uploads/conducteurs/`, `public/uploads/signatures/`,
      `public/uploads/logos/`, `storage/logs/`.
- [ ] A backup strategy exists for the Postgres database. Nothing in this
      app backs it up automatically — at minimum, a nightly
      `pg_dump min_transport | gzip > backup-$(date +%F).sql.gz` on a cron
      job (host cron for bare-metal, or `docker compose exec db pg_dump ...`
      wrapped in a host cron job for Docker), rotated/retained somewhere off
      the VPS itself.
- [ ] PWA assets are actually being served:
      `curl -I https://your-domain.example/public/manifest.json` and
      `curl -I https://your-domain.example/public/sw.js` should both return
      `200`, not `404` (an Nginx `deny` rule scoped too broadly under
      `/public/` would break this).

---

## Rollback

No migration framework exists, so rolling back a bad deploy is git- (or
image-) level, not schema-aware:

- **Docker**: `git checkout <previous-good-commit>` and
  `docker compose up -d --build` (or re-tag/redeploy the previous known-good
  image if you're tagging builds).
- **Bare-metal**: `git checkout <previous-good-commit>` in the deployed
  checkout, re-run `composer install --no-dev --optimize-autoloader` if
  `composer.lock` changed, reload PHP-FPM.
- **If the bad deploy included a schema change**: restore the most recent
  `pg_dump` backup taken before the deploy — `database/schema.sql`'s
  `IF NOT EXISTS`/idempotent style makes it safe to re-run, but it has no
  concept of reverting an already-applied `ALTER TABLE` or column drop, so a
  backup is the only real undo.
