ARG PHP_VERSION=8.1
ARG ALPINE_VERSION=3.16
ARG NGINX_VERSION=1.23
ARG GID=2432

FROM php:${PHP_VERSION}-fpm-alpine${ALPINE_VERSION} as php_base

RUN apk update --no-cache && apk add --no-cache \
    acl \
    git \
    unzip \
    zip

COPY --from=mlocati/php-extension-installer:1.5 /usr/bin/install-php-extensions /usr/local/bin/

RUN install-php-extensions \
    json \
    intl \
    opcache \
    pdo \
    pdo_mysql \
    amqp

COPY --from=composer:2 /usr/bin/composer /usr/local/bin/composer

COPY .docker/php/php.ini /usr/local/etc/php/php.ini
COPY .docker/php/php-cli.ini /usr/local/etc/php/php-cli.ini

FROM php_base as owl_php
WORKDIR /var/www
COPY composer.* symfony.lock ./
RUN set -eux; \
    composer install --prefer-dist --no-autoloader --no-scripts --no-progress; \
    composer clear-cache

COPY . .

COPY .docker/php/api-entrypoint.sh /usr/local/bin/api-entrypoint
RUN chmod +x /usr/local/bin/api-entrypoint

ENTRYPOINT ["/usr/local/bin/api-entrypoint"]
CMD ["php-fpm"]

RUN setfacl -R -m u:www-data:rwX -m u:"$(whoami)":rwX apps/api/var
RUN setfacl -dR -m u:www-data:rwX -m u:"$(whoami)":rwX apps/api/var

FROM nginx:${NGINX_VERSION}-alpine AS owl_nginx

COPY .docker/nginx/conf.d/api.conf /etc/nginx/conf.d/
RUN rm /etc/nginx/conf.d/default.conf

WORKDIR /var/www

COPY --from=owl_php /var/www/apps/api/public apps/api/public/