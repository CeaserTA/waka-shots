# syntax=docker/dockerfile:1

# --- PHP dependencies ------------------------------------------------------
FROM composer:2 AS vendor
WORKDIR /app
COPY composer.json composer.lock ./
# Platform extensions are checked in the final image, not in this build-only stage.
RUN composer install --no-dev --no-scripts --no-autoloader --prefer-dist --no-interaction --ignore-platform-reqs
COPY . .
RUN composer dump-autoload --no-dev --optimize --no-scripts

# --- Front-end assets ------------------------------------------------------
# Needs vendor/: the Filament admin theme imports CSS from vendor/filament.
FROM node:22-alpine AS assets
WORKDIR /app
COPY package.json package-lock.json ./
RUN npm ci
COPY . .
COPY --from=vendor /app/vendor ./vendor
RUN npm run build

# --- Runtime: PHP-FPM + Nginx on :8080 -------------------------------------
FROM serversideup/php:8.3-fpm-nginx

USER root
RUN install-php-extensions intl gd bcmath \
    && php -r 'foreach (["intl", "gd", "bcmath", "zip", "pdo_mysql", "mbstring", "curl"] as $e) { if (! extension_loaded($e)) { fwrite(STDERR, "Missing PHP extension: $e\n"); exit(1); } }'
USER www-data

ENV PHP_OPCACHE_ENABLE=1

WORKDIR /var/www/html
COPY --chown=www-data:www-data . .
COPY --chown=www-data:www-data --from=vendor /app/vendor ./vendor
COPY --chown=www-data:www-data --from=assets /app/public/build ./public/build

RUN mkdir -p storage/app/public storage/app/temp storage/framework/cache/data storage/framework/sessions storage/framework/views storage/logs bootstrap/cache \
    && php artisan package:discover --ansi
