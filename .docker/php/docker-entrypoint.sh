#!/bin/sh

set -e

# first arg is `-f` or `--some-option`
if [ "${1#-}" != "$1" ]; then
	set -- php-fpm "$@"
fi

if [ "$1" = 'php-fpm' ] || [ "$1" = 'bin/console' ]; then
	mkdir -p var/cache var/log public/media

  cp .env .env.local

  composer install

	until bin/console doctrine:query:sql "select 1" >/dev/null 2>&1; do
	    (>&2 echo "Waiting for MySQL to be ready...")
		sleep 1
	done

  bin/console doctrine:database:drop --no-interaction --if-exists --force
	bin/console doctrine:database:create --no-interaction
	bin/console doctrine:schema:create --no-interaction

  bin/console sylius:fixtures:load --no-interaction

  bin/console assets:install --no-interaction
  mkdir -p public/_themes/owl/admin
  bin/console sylius:theme:assets:install public/_themes/owl/admin --no-interaction

fi

exec docker-php-entrypoint "$@"