#!/usr/bin/env bash

sudo add-apt-repository -y ppa:ubuntu-toolchain-r/test
sudo apt-get update -qq
sudo apt-get -qq install g++-4.8 hhvm-dev libboost-dev libboost-filesystem-dev libboost-program-options-dev libboost-regex-dev libboost-system-dev libboost-thread-dev libboost-context-dev
export CXX="g++-4.8" CC="gcc-4.8"

cd /home/travis/build/facile-it/paraunit/.travis/hhvm

hphpize
cmake .
make -j
make install

echo "hhvm.dynamic_extensions[sigsegv] = sigsegv.so" > /etc/hhvm/php.ini
