# Makefile for Insight Core Project
setup: build composer-update

shell: build
	docker compose run --rm php zsh

build: 
	docker compose build php

start:
	docker compose up --wait php

composer-update: start
	docker compose exec php composer update --ignore-platform-req=php+

pre-commit-check: rector cs-fix psalm phpstan tests composer-check

rector: start
	docker compose exec php vendor/bin/rector --ansi

cs-fix: start
	docker compose exec php vendor/bin/php-cs-fixer fix --verbose --ansi

psalm: start
	docker compose exec php vendor/bin/psalm

phpstan: start
	docker compose exec php vendor/bin/phpstan analyse --ansi --memory-limit=-1

tests: start
	docker compose exec php vendor/bin/phpunit --colors=always

composer-check: composer-dependency-analyzer

composer-dependency-analyzer: start
	docker compose exec php vendor/bin/composer-dependency-analyser

composer-validate: start
	docker compose exec php composer validate --strict --ansi
