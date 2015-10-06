#!/usr/bin/env bash

apt-get install hhvm-dev g++ -y
cd /home/travis/build/facile-it/paraunit/.travis/hhvm

hphpize
cmake .
make -j
make install

echo "hhvm.dynamic_extensions[sigsegv] = sigsegv.so" > /etc/hhvm/php.ini
