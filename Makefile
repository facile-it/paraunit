# Makefile for Insight Core Project
shell: build
	docker-compose run --rm php zsh

build: 
	docker-compose build php

setup: build composer-install

composer-install:
	docker-compose run --rm php composer install
