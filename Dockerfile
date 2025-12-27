FROM php:8.2-apache

RUN apt-get update && apt-get install -y unzip git libzip-dev \
 && docker-php-ext-install pdo pdo_mysql zip \
 && a2enmod rewrite

WORKDIR /var/www/html

COPY . .

RUN chown -R www-data:www-data storage bootstrap/cache

RUN sed -i 's#/var/www/html#/var/www/html/public#g' /etc/apache2/sites-available/000-default.conf

ENV PORT=8080
RUN sed -i 's/80/${PORT}/g' /etc/apache2/ports.conf /etc/apache2/sites-available/000-default.conf

EXPOSE 8080

CMD ["apache2-foreground"]
