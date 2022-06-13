# Makefile for Insight Core Project
setup: build composer-install

shell: build
	docker-compose run --rm php zsh

build: 
	docker-compose build php

start:
	docker-compose up -d php

composer-install: start
	docker-compose exec php composer install

pre-commit-check: cs-fix phpstan tests

cs-fix: start
	docker-compose exec php bin/php-cs-fixer fix --verbose --ansi

phpstan: start
	docker-compose exec php bin/phpstan analyse --ansi --memory-limit=-1

tests: start
	docker-compose exec php bin/phpunit --colors=always
