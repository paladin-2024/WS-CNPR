#!/bin/sh
set -e

echo "=== CNPR Portal - Container Entrypoint ==="
echo "Environment: ${APP_ENV:-production}"

# Wait for PostgreSQL to be ready
echo "Waiting for PostgreSQL..."
until pg_isready -h "${DB_HOST:-postgres}" -p "${DB_PORT:-5432}" -U "${DB_USERNAME:-transport_app}" -d "${DB_DATABASE:-min_transport}" > /dev/null 2>&1; do
    sleep 2
done
echo "PostgreSQL is ready!"

# Run database seed/initialization
echo "Running database initialization..."
cd /var/www/html
php database/seed.php

# Create storage directories if they don't exist. public/uploads/* are each
# their own named volume (docker-compose.yml) mounted writable inside the
# otherwise read-only ./public bind mount - the uploads/ parent directory
# itself is NOT writable, so it must never be chown/chmod'd, only its
# subdirectories (each an independent, already-writable mount point).
mkdir -p /var/www/html/storage/logs
mkdir -p /var/www/html/public/uploads/conducteurs
mkdir -p /var/www/html/public/uploads/signatures
mkdir -p /var/www/html/public/uploads/logos
mkdir -p /var/www/html/public/uploads/signalements

# Set permissions
chown -R www-data:www-data /var/www/html/storage
chmod -R 755 /var/www/html/storage
for dir in conducteurs signatures logos signalements; do
    chown -R www-data:www-data /var/www/html/public/uploads/"$dir"
    chmod -R 755 /var/www/html/public/uploads/"$dir"
done

# Clear opcache
php -r "if (function_exists('opcache_reset')) opcache_reset();"

echo "=== Initialization complete. Starting PHP-FPM ==="

# Execute the main command (php-fpm by default)
exec "$@"