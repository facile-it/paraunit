#!/bin/bash

DIR=$( cd "$( dirname "${BASH_SOURCE[0]}" )" && pwd )

docker build -t paraunit_image $DIR

docker rm -f paraunit_container
docker run -d -v $DIR/../:/home/paraunit/projects --name paraunit_container -ti paraunit_image

sleep 1

docker exec -ti -u paraunit paraunit_container zsh
