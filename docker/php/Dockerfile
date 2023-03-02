FROM php:8.2.1-zts-bullseye AS php

RUN apt-get update -y \
    && apt-get upgrade -y

RUN apt-get install git libsodium-dev libzip-dev libpq-dev -y
RUN docker-php-ext-install sodium zip pdo pdo_pgsql

RUN mv "$PHP_INI_DIR/php.ini-development" "$PHP_INI_DIR/php.ini"

COPY --from=composer:2.1 /usr/bin/composer /usr/bin/composer

FROM php AS app

RUN mkdir /app
COPY src /app/src
COPY tests /app/tests
COPY composer.json phpunit.xml /app/

WORKDIR /app

RUN COMPOSER_ALLOW_SUPERUSER=1 composer validate \
    && composer install --no-scripts

CMD ["/app/vendor/bin/phpunit"]
