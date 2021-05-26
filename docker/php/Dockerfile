FROM php:8.0-fpm

RUN apt-get update \
    && apt-get install -y \
    libzip-dev

RUN docker-php-ext-install \
    pdo_mysql zip

COPY --from=composer /usr/bin/composer /usr/bin/composer    

WORKDIR /var/www/app/

CMD composer install ; \
    php-fpm