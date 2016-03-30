#!/usr/bin/env bash

echo "Paraunit: Tidyng up the code..."

phpcs -p --standard=PSR2 --colors  src/
phpcbf --standard=PSR2 src/

phpcs -p --standard=PSR2 --colors  tests/
phpcbf --standard=PSR2 tests/

echo "Paraunit: DONE! You can now submit you PR!"
