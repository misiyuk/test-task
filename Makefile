#!make

up:
	docker-compose up -d --build

init: up
	docker-compose exec php composer install
	docker-compose exec php bin/console doctrine:migrations:migrate --no-interaction

sleep:
	sleep 15

down:
	docker-compose down --remove-orphans

php:
	docker-compose exec php bash

m:
	docker-compose exec php bin/console doctrine:migrations:diff

migrate:
	docker-compose exec php bin/console doctrine:migrations:migrate --no-interaction

my:
	sudo chown -R $$USER:$$USER src config vendor tests templates translations migrations

test: up
	docker-compose exec php bin/phpunit
