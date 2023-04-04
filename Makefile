phpunit:
	./vendor/bin/phpunit

psalm:
	./vendor/bin/psalm

install:
	composer install --no-interaction --no-scripts

lint:
	./vendor/bin/ecs

lint-fix:
	./vendor/bin/ecs --fix

clear-cache: 
	@rm -rf apps/*/*/var
	./apps/api/bin/console cache:warmup

docker-build: CMD=up --build -d

docker-start: CMD=up -d

docker-stop: CMD=stop

docker-destroy: CMD=down

docker-rebuild:
	docker-compose build --pull --force-rm --no-cache
	make docker-build

docker docker-build docker-start docker-stop docker-destroy:
	UID=${shell id -u} GID=${shell id -g} docker-compose $(CMD)

docker-apps-clear-cache:
	@rm -rf apps/*/*/var
	@docker exec owl-clean_architecture-php ./apps/api/bin/console cache:warmup