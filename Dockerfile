ARG PHP_VERSION=8.0
ARG ALPINE_VERSION=3.16
ARG NODE_VERSION=16
ARG NGINX_VERSION=1.23
ARG GID=2432

FROM php:${PHP_VERSION}-fpm-alpine${ALPINE_VERSION} as php_base

RUN apk update --no-cache && apk add --no-cache \
    git \
    unzip \
    zip

COPY --from=mlocati/php-extension-installer:1.5 /usr/bin/install-php-extensions /usr/local/bin/

RUN install-php-extensions \
    json \
    intl \
    opcache \
    pdo \
    pdo_mysql

COPY --from=composer:2 /usr/bin/composer /usr/local/bin/composer

COPY docker/php/php.ini /usr/local/etc/php/php.ini
COPY docker/php/php-cli.ini /usr/local/etc/php/php-cli.ini

FROM php_base as owl_php
WORKDIR /var/www
COPY composer.* symfony.lock ./
RUN set -eux; \
    composer install --prefer-dist --no-autoloader --no-scripts --no-progress; \
    composer clear-cache

COPY . .

COPY docker/php/docker-entrypoint.sh /usr/local/bin/docker-entrypoint
RUN chmod +x /usr/local/bin/docker-entrypoint

ENTRYPOINT ["/usr/local/bin/docker-entrypoint"]
CMD ["php-fpm"]


FROM nginx:${NGINX_VERSION}-alpine AS owl_nginx

COPY docker/nginx/conf.d/default.conf /etc/nginx/conf.d/

WORKDIR /var/www

COPY --from=owl_php /var/www/public public/