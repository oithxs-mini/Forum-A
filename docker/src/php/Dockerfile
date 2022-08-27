FROM php:fpm-buster

COPY ./docker/src/php/php.ini /usr/local/etc/php/

COPY ./ /var/www/forum

RUN apt-get update && apt-get install -y \
    unzip

RUN docker-php-ext-install mysqli pdo_mysql

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

ENV COMPOSER_ALLOW_SUPERUSER 1

ENV COMPOSER_HOME /composer

ENV PATH $PATH:/composer/vendor/bin

WORKDIR /var/www/forum

RUN composer install