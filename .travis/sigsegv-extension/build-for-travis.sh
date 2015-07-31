#!/usr/bin/env bash

cd /home/travis/build/facile-it/paraunit/.travis/sigsegv-extension

phpize
./configure --enable-sigsegv
make -j

phpenv config-add /home/travis/build/facile-it/paraunit/.travis/sigsegv.ini

cd /home/travis/build/facile-it/paraunit/