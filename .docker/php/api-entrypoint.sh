#!/bin/sh

set -e

# first arg is `-f` or `--some-option`
if [ "${1#-}" != "$1" ]; then
	set -- php-fpm "$@"
fi

if [ "$1" = 'php-fpm' ] || [ "$1" = 'bin/console' ]; then
	mkdir -p apps/api/var/cache apps/api/var/log

	if [ ! -e ".env.local" ]; then
		cp -p .env .env.local
	fi

	composer install

	until apps/api/bin/console doctrine:query:sql "select 1" >/dev/null 2>&1; do
	    (>&2 echo "Waiting for MySQL to be ready...")
		sleep 1
	done

	apps/api/bin/console doctrine:database:create --if-not-exists --no-interaction
	apps/api/bin/console doctrine:schema:update --complete --force --no-interaction

	apps/api/bin/console assets:install --no-interaction

fi

exec docker-php-entrypoint "$@"