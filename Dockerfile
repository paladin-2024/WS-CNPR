# syntax=docker/dockerfile:1
#
# Portail Numérique du Ministère des Transports — production image.
#
# Multi-stage build:
#   1. `vendor` — installs Composer dependencies (phpoffice/phpspreadsheet) in an
#      isolated stage so the final image never contains Composer itself or the
#      package download cache.
#   2. final stage — php:8.3-fpm with only the PHP extensions the app actually
#      needs, running as php-fpm (listens on 9000, fronted by the `nginx`
#      service in docker-compose.yml).
#
# PHP version note: composer.json itself pins no "php" constraint, but its only
# dependency, phpoffice/phpspreadsheet ^5.5, requires php ^8.1 (see
# composer.lock). 8.3 is chosen as the newest release satisfying that
# constraint with the longest remaining active support window as of writing;
# bump the two FROM lines below together if that changes.

FROM composer:2 AS vendor

# Install PHP extensions needed by phpoffice/phpspreadsheet for composer install
# composer:2 is based on php:8.3-cli-alpine, so we use apk + docker-php-ext-install
RUN apk add --no-cache \
        postgresql-dev \
        libpng-dev \
        libjpeg-turbo-dev \
        freetype-dev \
        libwebp-dev \
        libzip-dev \
        oniguruma-dev \
        icu-dev \
        unzip \
    && docker-php-ext-configure gd --with-freetype --with-jpeg --with-webp \
    && docker-php-ext-install -j"$(nproc)" \
        pdo_pgsql \
        gd \
        zip \
        mbstring \
        intl \
        bcmath

WORKDIR /app

# Copy only the dependency manifests first so this layer is cached until
# composer.json/composer.lock actually change.
COPY composer.json composer.lock ./

RUN composer install \
        --no-dev \
        --no-scripts \
        --no-interaction \
        --no-progress \
        --optimize-autoloader \
        --prefer-dist


FROM php:8.3-fpm AS app

# --- System packages + PHP extensions -------------------------------------
#
# Required by the app itself:
#   pdo_pgsql  - app/Core/Database.php connects to PostgreSQL via PDO
#   curl       - app/Core/SmsService.php calls the Dream Digital SMS API
#
# Required by phpoffice/phpspreadsheet (see its composer.json "require"):
#   ext-gd, ext-zip, ext-mbstring - not compiled into the base php:8.3-fpm
#     image and need explicit installation
#   ext-ctype, ext-dom, ext-fileinfo, ext-filter, ext-iconv, ext-libxml,
#   ext-simplexml, ext-xml, ext-xmlreader, ext-xmlwriter, ext-zlib - already
#     enabled by default in the official php-fpm image, nothing to do
#
# opcache is installed too (standard production performance win for PHP-FPM).
# intl and bcmath added for potential future needs (currency, i18n)
#
# NOTE: earlier this stage purged the -dev packages after compiling (via
# apt-get purge --auto-remove) to shrink the image. That's a known footgun:
# apt's autoremove doesn't stop at the -dev/header-only packages, it cascades
# to the RUNTIME shared libraries too (libpq.so.5, libpng16.so.16,
# libicuio.so.<ver>, libzip.so.<ver>, etc.) since nothing else in the image
# declares a dependency on them once the -dev package is gone - the compiled
# extensions were then left with no shared library to dlopen at runtime
# ("Unable to load dynamic library ... cannot open shared object file"),
# reproduced live on the deploy VPS. Simplest reliable fix: don't purge.
# Costs a bit of image size, guarantees correctness across base-image updates
# (the exact runtime package names/versions - e.g. libicu72 vs libicu76 -
# vary by Debian release and would silently break this again otherwise).
RUN apt-get update && apt-get install -y --no-install-recommends \
        libpq-dev \
        libpng-dev \
        libjpeg62-turbo-dev \
        libfreetype6-dev \
        libwebp-dev \
        libzip-dev \
        libonig-dev \
        libcurl4-openssl-dev \
        libicu-dev \
        unzip \
    && docker-php-ext-configure gd --with-freetype --with-jpeg --with-webp \
    && docker-php-ext-install -j"$(nproc)" \
        pdo_pgsql \
        gd \
        zip \
        mbstring \
        curl \
        intl \
        bcmath \
        opcache \
    && rm -rf /var/lib/apt/lists/*

# Recommended production opcache settings. validate_timestamps=0 means PHP
# source changes require a container rebuild/restart to take effect, which is
# the expected deployment model for an immutable image.
RUN { \
        echo 'opcache.memory_consumption=128'; \
        echo 'opcache.interned_strings_buffer=16'; \
        echo 'opcache.max_accelerated_files=10000'; \
        echo 'opcache.validate_timestamps=0'; \
        echo 'opcache.revalidate_freq=0'; \
    } > /usr/local/etc/php/conf.d/opcache-recommended.ini

# The app handles photo/signature/logo uploads (see app/Controllers/
# AdminController.php, ConfigController.php, VerificationController.php);
# the stock 2M/8M php.ini defaults are too small for typical driver photos.
RUN { \
        echo 'upload_max_filesize=10M'; \
        echo 'post_max_size=12M'; \
        echo 'memory_limit=256M'; \
    } > /usr/local/etc/php/conf.d/app-uploads.ini

WORKDIR /var/www/html

# Vendor deps from the build stage (no Composer, no download cache in the
# final image).
COPY --from=vendor /app/vendor ./vendor

# Application source.
COPY . .

# storage/ (logs) and public/uploads/ (conducteur photos, signatures, logos)
# must be writable by the php-fpm worker user (www-data, per the base image's
# default www-data pool config in /usr/local/etc/php-fpm.d/www.conf). Both
# directories are also expected to be replaced by named volumes at runtime
# (see docker-compose.yml) so container recreation doesn't lose data — the
# ownership set here matters for the volume's initial contents and for any
# subdirectory PHP creates on the fly.
RUN mkdir -p storage/logs \
        public/uploads/conducteurs \
        public/uploads/signatures \
        public/uploads/logos \
    && chown -R www-data:www-data storage public/uploads \
    && chmod -R 775 storage public/uploads

# Entrypoint for DB initialization and permissions
COPY docker-entrypoint.sh /usr/local/bin/docker-entrypoint.sh
RUN chmod +x /usr/local/bin/docker-entrypoint.sh

EXPOSE 9000

ENTRYPOINT ["docker-entrypoint.sh"]
CMD ["php-fpm"]