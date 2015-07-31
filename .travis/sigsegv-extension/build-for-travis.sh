#!/usr/bin/env bash

phpize /home/travis/build/facile-it/paraunit/.travis/sigsegv-extension
cd home/travis/build/facile-it/paraunit/.travis/sigsegv-extension && ./configure --enable-sigsegv
make -j /home/travis/build/facile-it/paraunit/.travis/sigsegv-extension

phpenv config-add ./home/travis/build/facile-it/paraunit/.travis/sigsegv.ini