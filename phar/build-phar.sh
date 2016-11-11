#!/bin/bash

DIR=$( cd "$( dirname "${BASH_SOURCE[0]}" )" && pwd )

php -d phar.readonly=0 "${DIR}/build-phar.php"
