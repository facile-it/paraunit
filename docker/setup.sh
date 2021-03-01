#!/bin/bash

set -e

DOCKER_DIR=$( cd "$( dirname "${BASH_SOURCE[0]}" )" && pwd )
COMPOSER_DIR=~/.composer
mkdir -p $COMPOSER_DIR

docker build -t paraunit_container $DOCKER_DIR
docker run --rm -ti \
  -v $DOCKER_DIR/../:/home/paraunit/projects \
  -v $COMPOSER_DIR:/home/paraunit/.composer \
  -u paraunit \
  paraunit_container zsh
