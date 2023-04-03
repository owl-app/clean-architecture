phpunit:
	vendor/bin/phpunit

psalm:
	vendor/bin/psalm

install:
	composer install --no-interaction --no-scripts

lint:
	./tools/php-cs-fixer fix --config .php-cs-fixer.dist.php --allow-risky=yes --dry-run