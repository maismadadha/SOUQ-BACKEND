# ---------- Stage 1: Composer dependencies ----------
FROM composer:2 AS vendor
WORKDIR /app

# Copy only composer files first (better caching)
COPY composer.json composer.lock ./
RUN composer install --no-dev --prefer-dist --no-interaction --no-progress

# Copy the full project and optimize autoload
COPY . .
RUN composer dump-autoload --optimize


# ---------- Stage 2: Runtime image ----------
FROM php:8.2-apache

# Enable Apache rewrite
RUN a2enmod rewrite

# Install PHP extensions needed for Laravel + MySQL
RUN apt-get update && apt-get install -y \
    libzip-dev unzip \
 && docker-php-ext-install pdo pdo_mysql \
 && rm -rf /var/lib/apt/lists/*

# Set document root to /public
ENV APACHE_DOCUMENT_ROOT=/var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf \
    && sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

WORKDIR /var/www/html

# Copy app (including vendor from stage 1)
COPY --from=vendor /app /var/www/html

# Permissions (Laravel storage/cache)
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

EXPOSE 8080

# Cloud Run uses PORT env var
RUN sed -i 's/Listen 80/Listen 8080/g' /etc/apache2/ports.conf /etc/apache2/sites-available/000-default.conf

CMD ["apache2-foreground"]
