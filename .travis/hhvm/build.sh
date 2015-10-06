#!/usr/bin/env bash

cd /home/travis/build/facile-it/paraunit/.travis/hhvm

hphpize
cmake .
make -j

echo "extension=sigsegv.so" >> /etc/hhvm/php.ini
