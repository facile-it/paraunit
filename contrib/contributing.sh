#!/bin/sh

echo "### Paraunit: Tidyng up the code ###"

./vendor/bin/phpcbf --standard=PSR2 src/
./vendor/bin/phpcbf --standard=PSR2 tests/

echo "### Paraunit: DONE! ###"