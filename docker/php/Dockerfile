FROM php:8.4-fpm-alpine

RUN echo "expose_php=Off" > /usr/local/etc/php/conf.d/99-misc.ini

ARG WORKDIR=/var/www/lodfe
WORKDIR ${WORKDIR}

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer
ADD --chown=www-data:www-data . /var/www/lodfe
RUN composer install
