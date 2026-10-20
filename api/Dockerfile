# Laravel 13 + Filament admin + API. nginx + php-fpm (serversideup image), queue worker and scheduler in the same container.
FROM composer:2 AS vendor
WORKDIR /app
COPY composer.json composer.lock ./
RUN composer install --no-dev --no-scripts --no-interaction --prefer-dist --optimize-autoloader --ignore-platform-reqs

FROM serversideup/php:8.3-fpm-nginx
ENV PHP_OPCACHE_ENABLE=1 AUTORUN_ENABLED=true AUTORUN_LARAVEL_MIGRATION=true AUTORUN_LARAVEL_STORAGE_LINK=true \
    LOG_CHANNEL=stderr SSL_MODE=off NGINX_WEBROOT=/var/www/html/public
USER root
RUN install-php-extensions intl gd zip bcmath pdo_pgsql redis exif pcntl
WORKDIR /var/www/html
COPY --chown=www-data:www-data . .
COPY --chown=www-data:www-data --from=vendor /app/vendor ./vendor
COPY docker/entrypoint.d/ /etc/entrypoint.d/
RUN chmod +x /etc/entrypoint.d/*.sh && mkdir -p storage/app/public && chown -R www-data:www-data storage bootstrap/cache
USER www-data
