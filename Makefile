#!make

up:
	docker-compose up -d --build

init: up
	docker-compose exec php composer install
	docker-compose exec php bin/console doctrine:migrations:migrate --no-interaction
