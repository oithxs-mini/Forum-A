FROM php:fpm-buster

COPY ./docker/src/php/php.ini /usr/local/etc/php/

COPY ./docker/src/php/www.conf /usr/local/etc/php-fpm.d/

COPY ./ /usr/share/nginx/html

RUN apt-get update && apt-get install -y \
    unzip \
    && rm -rf /var/lib/apt/lists/*

RUN docker-php-ext-install mysqli pdo_mysql

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

ENV COMPOSER_ALLOW_SUPERUSER 1

ENV COMPOSER_HOME /composer

ENV PATH $PATH:/composer/vendor/bin

WORKDIR /usr/share/nginx/html

RUN composer install