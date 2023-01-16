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

pre-commit-check: rector cs-fix psalm phpstan tests

rector: start
	docker-compose exec php vendor/bin/rector --ansi

cs-fix: start
	docker-compose exec php vendor/bin/php-cs-fixer fix --verbose --ansi

psalm: start
	docker-compose exec php vendor/bin/psalm

phpstan: start
	docker-compose exec php vendor/bin/phpstan analyse --ansi --memory-limit=-1

tests: start
	docker-compose exec php vendor/bin/phpunit --colors=always
