# deploy/

Production Nginx + PHP-FPM config examples for the Portail Numérique du
Ministère des Transports app. This is local documentation for the two files
in this directory only — a fuller end-to-end deployment guide is being
written separately.

## Files

- **`nginx.conf.example`** — a complete Nginx server block: HTTP→HTTPS
  redirect on port 80 (with an ACME challenge passthrough for certbot), and
  a commented-out HTTPS server block on port 443 containing the actual
  application config (routing to `index.php`, static asset caching, upload
  serving, PHP-FPM proxying, and explicit denies for `.env`, `.git`,
  `vendor/`, `storage/`, `database/`, `app/`, `config/`, etc.).
- **`php-fpm-pool.conf.example`** — a PHP-FPM pool file covering the
  directives that matter for this app specifically: upload size limits
  (kept in sync with Nginx's `client_max_body_size`), `memory_limit` and
  `max_execution_time` sized for BrevetController's unbounded
  Excel/photo/QR-code exports, session cookie hardening, and disabling
  error/version disclosure in production.

## How they fit together

Nginx terminates TLS and serves static files directly; everything else
(including the single `index.php` front controller) is proxied to PHP-FPM
over a unix socket. Both files reference that same socket path — if you
change one, change the other:

- `nginx.conf.example`: `fastcgi_pass` → `unix:/run/php/CHANGE_ME-fpm.sock`
- `php-fpm-pool.conf.example`: `listen` → `/run/php/CHANGE_ME-fpm.sock`

They also share the upload size limit — `client_max_body_size` in the Nginx
file must stay `>=` `upload_max_filesize`/`post_max_size` in the pool file,
otherwise uploads fail inconsistently depending on which layer rejects
first.

## Where to drop them

1. **Nginx**: copy `nginx.conf.example` to
   `/etc/nginx/sites-available/portail-transport.conf`, replace every
   `CHANGE_ME*` placeholder (domain, project root path, PHP-FPM socket
   name), then symlink it into `sites-enabled/`:
   ```
   sudo ln -s /etc/nginx/sites-available/portail-transport.conf \
              /etc/nginx/sites-enabled/portail-transport.conf
   sudo nginx -t   # validate syntax before reloading
   sudo systemctl reload nginx
   ```
   Leave the 443 block commented out until certbot has actually issued a
   certificate (the port-80 block's ACME challenge location is enough for
   `certbot certonly --webroot` to work without the 443 block existing
   yet). Once the cert exists, uncomment the 443 block, re-run
   `nginx -t`, then reload again.

2. **PHP-FPM**: copy `php-fpm-pool.conf.example` to your PHP-FPM pool
   directory — typically `/etc/php/<version>/fpm/pool.d/portail-transport.conf`
   on Debian/Ubuntu (check the actual installed version's path; this repo
   was developed against PHP 8.4). Replace the `CHANGE_ME_php_fpm_user`/
   `CHANGE_ME_php_fpm_group`/socket placeholders, then:
   ```
   sudo php-fpm8.4 -t          # or whatever version's binary — validate config
   sudo systemctl restart php8.4-fpm
   ```

## Things the person deploying this should double-check

- **Upload size (10M default)**: nothing in the application code enforces
  a server-side upload size limit except a 5MB check on one citizen-facing
  form (`VerificationController::signalerFraude`). The admin conducteur
  photo/ID-scan and logo/signature uploads have no code-side ceiling at
  all, so 10M here is a judgment call, not a value read out of the app —
  raise it in both files together if real-world document scans run larger.
- **`memory_limit` (256M) / `max_execution_time` (300s)**: sized around
  `BrevetController`'s unbounded (no `LIMIT`) Excel/photo/QR-code export
  endpoints, one of which (`downloadQrcodes()`) makes a serial outbound
  HTTP call per record. Neither value is backed by a measurement — treat
  them as a starting point and watch the PHP-FPM error log after a few
  real exports.
- **`session.cookie_secure`**: only enable once the 443 (HTTPS) block is
  actually live — turning it on while still serving over plain HTTP will
  silently break login by having the browser drop every session cookie.
- **Subdirectory deployment**: the shipped config assumes root-level
  deployment (`https://domain/`). See the comment block in
  `nginx.conf.example` above the `root` directive for what changes if this
  app is instead deployed under a path prefix.
