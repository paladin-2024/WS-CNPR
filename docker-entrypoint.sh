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

# Create storage directories if they don't exist
mkdir -p /var/www/html/storage/logs
mkdir -p /var/www/html/public/uploads/conducteurs
mkdir -p /var/www/html/public/uploads/signatures
mkdir -p /var/www/html/public/uploads/logos

# Set permissions
chown -R www-data:www-data /var/www/html/storage /var/www/html/public/uploads
chmod -R 755 /var/www/html/storage /var/www/html/public/uploads

# Clear opcache
php -r "if (function_exists('opcache_reset')) opcache_reset();"

echo "=== Initialization complete. Starting PHP-FPM ==="

# Execute the main command (php-fpm by default)
exec "$@"