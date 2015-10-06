#!/usr/bin/env bash

sudo add-apt-repository -y ppa:ubuntu-toolchain-r/test
sudo apt-get -qq update
sudo apt-get -qq install g++-4.8 hhvm-dev

cd /home/travis/build/facile-it/paraunit/.travis/hhvm

hphpize
cmake .
make -j
make install

echo "hhvm.dynamic_extensions[sigsegv] = sigsegv.so" > /etc/hhvm/php.ini
