FROM php:8.1-fpm-alpine

ARG WORKDIR=/var/www/lodfe
WORKDIR ${WORKDIR}

COPY --from=composer /usr/bin/composer /usr/bin/composer
ADD --chown=www-data:www-data . /var/www/lodfe
RUN composer install
