#!/bin/bash

DIR=$( cd "$( dirname "${BASH_SOURCE[0]}" )" && pwd )

docker build -f $DIR/dockerfile-php-5.6 -t paraunit_image_php_5_6 $DIR

docker rm -f paraunit_container_php_5_6
docker run -d -v $DIR/../:/home/paraunit/projects --name paraunit_container_php_5_6 -ti paraunit_image_php_5_6 bash

sleep 1

docker exec -ti -u paraunit paraunit_container_php_5_6 zsh
