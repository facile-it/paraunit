#!/usr/bin/env bash

sudo apt-get install hhvm-dev g++4.8

cd /home/travis/build/facile-it/paraunit/.travis/hhvm

hphpize
cmake .
make -j
make install

echo "hhvm.dynamic_extensions[sigsegv] = sigsegv.so" > /etc/hhvm/php.ini
