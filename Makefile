phpunit:
	vendor/bin/phpunit

psalm:
	vendor/bin/psalm

install:
	composer install --no-interaction --no-scripts

lint:
	./vendor/bin/ecs

lint-fix:
	./vendor/bin/ecs --fix