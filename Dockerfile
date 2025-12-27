-----------------------------
Stage 1: Install Composer deps (PHP 8.2)
-----------------------------
FROM composer:2-php82 AS vendor

WORKDIR /app

Copy composer files first (better caching)
COPY composer.json composer.lock ./

Install production deps
RUN composer install --no-dev --prefer-dist --no-interaction --no-progress --optimize-autoloader

Copy the rest of the app (so autoload dumps can see files if needed)
COPY . .

(Optional) If you want, you can optimize autoload again after full copy
RUN composer dump-autoload --no-dev --optimize


-----------------------------
Stage 2: Runtime (PHP 8.2 + Apache)
-----------------------------
FROM php:8.2-apache

Install OS deps + PHP extensions needed for Laravel + MySQL
RUN apt-get update && apt-get install -y \
    unzip git libzip-dev \
    && docker-php-ext-install pdo pdo_mysql zip \
    && a2enmod rewrite headers \
    && rm -rf /var/lib/apt/lists/*

Cloud Run uses $PORT (not 80)
ENV PORT=8080
RUN sed -i 's/80/${PORT}/g' /etc/apache2/ports.conf /etc/apache2/sites-available/000-default.conf

Set Apache docroot to Laravel /public
ENV APACHE_DOCUMENT_ROOT=/var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf \
 && sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

WORKDIR /var/www/html

Copy app source
COPY . .

Copy vendor from Stage 1
COPY --from=vendor /app/vendor /var/www/html/vendor

Laravel permissions
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

EXPOSE 8080
CMD ["apache2-foreground"]
