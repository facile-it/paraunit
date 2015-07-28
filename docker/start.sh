#!/bin/bash

docker build -t paraunit_image .

docker stop paraunit_container
docker rm paraunit_container
docker run -d -v $PWD/../:/~/paraunit --name paraunit_container -t -i paraunit_image

sleep 5

docker exec -t -i -u paraunit paraunit_container zsh
