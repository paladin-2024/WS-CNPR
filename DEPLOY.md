# Deployment Guide: cnpr.wscsarl.info

## Prerequisites on VPS

- Ubuntu 22.04+ / Debian 12+
- Docker 24+ & Docker Compose v2+
- Domain `cnpr.wscsarl.info` pointing to VPS IP (A record)
- Ports 80 & 443 open on firewall

## Quick Start

```bash
# 1. Clone repo
git clone https://github.com/paladin-2024/WS-CNPR.git /opt/cnpr
cd /opt/cnpr

# 2. Create production .env from template
cp .env.production .env
nano .env  # Set DB_PASSWORD, SMS credentials

# 3. Build & start (first run takes 2-3 min)
docker compose up -d --build

# 4. Verify
docker compose logs -f app
docker compose logs -f nginx
```

## Initial SSL Certificate

On first run, certbot automatically obtains Let's Encrypt cert via HTTP-01 challenge (port 80).

If certbot fails, run manually:

```bash
docker compose run --rm certbot certonly \
  --webroot -w /var/www/certbot \
  -d cnpr.wscsarl.info \
  --email your@email.com \
  --agree-tos --no-eff-email
```

Then reload nginx:
```bash
docker compose exec nginx nginx -s reload
```

## Services

| Service | Port | Description |
|---------|------|-------------|
| nginx | 80, 443 | Reverse proxy, SSL termination, static files |
| app | 9000 | PHP-FPM application server (built on VPS) |
| postgres | 5432 | PostgreSQL database (internal only) |
| certbot | - | Auto-renewal of Let's Encrypt certs |

## Data Persistence

Docker volumes (survive container recreation):

- `postgres_data` — Database
- `uploads_conducteurs` — Driver photos
- `uploads_signatures` — Digital signatures
- `uploads_logos` — Card logos
- `storage_logs` — Application logs
- `certbot_etc` — SSL certificates
- `certbot_www` — ACME challenge files

## Common Commands

```bash
# View logs
docker compose logs -f [service]

# Restart a service
docker compose restart app

# Run migrations / seed DB manually
docker compose exec app php database/seed.php

# Backup database
docker compose exec postgres pg_dump -U transport_app min_transport > backup_$(date +%F).sql

# Restore database
docker compose exec -T postgres psql -U transport_app min_transport < backup.sql

# Update app
git pull
docker compose up -d --build

# Full reset (⚠️ destroys data)
docker compose down -v
docker compose up -d --build
```

## Environment Variables (.env)

| Variable | Required | Description |
|----------|----------|-------------|
| `DB_PASSWORD` | Yes | Strong PostgreSQL password |
| `SMS_API_ID` | No | Dream Digital SMS API ID |
| `SMS_API_PASSWORD` | No | Dream Digital SMS API password |
| `SMS_SENDER_ID` | No | Sender ID (default: CNPR-TSHOPO) |
| `SMS_DEBUG` | No | Enable SMS debug logging (true/false) |

## Troubleshooting

**SSL certificate not issuing:**
- Verify domain DNS points to VPS IP
- Check port 80 is accessible from internet
- Check certbot logs: `docker compose logs certbot`

**Database connection failed:**
- Verify `.env` has correct `DB_PASSWORD`
- Check postgres health: `docker compose exec postgres pg_isready`

**Permission errors on uploads:**
```bash
docker compose exec app chown -R www-data:www-data /var/www/html/public/uploads /var/www/html/storage
```

**PHP errors:**
```bash
docker compose logs app
```

## Security Notes

- Change default admin password after first login (`admin@transport.dev` / `admin123`)
- Use strong `DB_PASSWORD` (32+ chars)
- Keep `.env` out of git (already in .gitignore)
- Regularly update: `docker compose pull && docker compose up -d`

## File Structure on VPS

```
/opt/cnpr/
├── .env                    # Production secrets (gitignored)
├── docker-compose.yml
├── Dockerfile
├── docker-entrypoint.sh
├── nginx/
│   ├── conf.d/cnpr.wscsarl.info.conf
│   └── ssl/                # Optional: pre-existing certs
├── database/schema.sql     # Auto-run on first postgres start
└── ...app files...
```