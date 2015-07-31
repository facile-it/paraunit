#!/usr/bin/env bash

phpize /home/travis/build/facile-it/paraunit/.travis/sigsegv-extension
./home/travis/build/facile-it/paraunit/.travis/sigsegv-extension/configure --enable-sigsegv
make -j /home/travis/build/facile-it/paraunit/.travis/sigsegv-extension

phpenv config-add ./home/travis/build/facile-it/paraunit/docker/sigsegv/sigsegv.ini
