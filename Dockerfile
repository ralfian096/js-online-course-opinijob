FROM composer:2 AS vendor

WORKDIR /app/API
COPY API/ ./
RUN composer install --no-dev --optimize-autoloader --no-interaction --prefer-dist

FROM php:8.3-apache

RUN apt-get update \
    && apt-get install -y --no-install-recommends \
        libicu-dev \
        libonig-dev \
        libpq-dev \
        libxml2-dev \
        libzip-dev \
        unzip \
    && docker-php-ext-install \
        bcmath \
        intl \
        mbstring \
        pdo_mysql \
        pdo_pgsql \
        xml \
        zip \
    && a2dismod mpm_event mpm_worker || true \
    && a2enmod mpm_prefork rewrite \
    && rm -rf /var/lib/apt/lists/*

WORKDIR /var/www

COPY API/ /var/www/API/
COPY HTML/ /var/www/API/public/
COPY --from=vendor /app/API/vendor /var/www/API/vendor
COPY docker/apache-vhost.conf /etc/apache2/sites-available/000-default.conf
COPY docker/start-app.sh /usr/local/bin/start-app.sh

RUN chmod +x /usr/local/bin/start-app.sh \
    && chown -R www-data:www-data /var/www/API/storage /var/www/API/bootstrap/cache

EXPOSE 80

CMD ["start-app.sh"]
