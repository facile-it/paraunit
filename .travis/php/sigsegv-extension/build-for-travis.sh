#!/usr/bin/env bash

cd /home/travis/build/facile-it/paraunit/.travis/php/sigsegv-extension

phpize
./configure --enable-sigsegv
make -j
make install

phpenv config-add /home/travis/build/facile-it/paraunit/.travis/php/sigsegv.ini

cd /home/travis/build/facile-it/paraunit/
