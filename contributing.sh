#!/usr/bin/env bash

echo "Paraunit: Tidyng up the code..."

phpcs -p --standard=phpcs.ruleset.xml --colors  src/
phpcbf --standard=phpcs.ruleset.xml src/

echo "Paraunit: DONE! You can now submit you PR!"