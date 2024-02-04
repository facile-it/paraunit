#!/bin/sh
set -eu

composer remove --dev --no-update facile-it/facile-coding-standard
composer remove --dev --no-update phpspec/prophecy-phpunit
composer remove --dev --no-update psalm/plugin-phpunit
composer remove --dev --no-update psalm/plugin-symfony
composer remove --dev --no-update vimeo/psalm
composer require --dev --no-update 'phpunit/php-invoker:^4.0||^5.0'
composer require --dev --no-update 'sebastian/comparator:5.0.1 as 6.0.0'
composer require --dev --no-update 'sebastian/diff:5.1.0 as 6.0.0'
composer require --dev --no-update 'sebastian/exporter:5.1.1 as 6.0.0'
composer require --dev --no-update 'sebastian/recursion-context:5.0.0 as 6.0.0'
composer update

rm -fr tmp
mkdir tmp
echo '{}' > tmp/composer.json
composer require --working-dir=tmp 'phpspec/prophecy-phpunit:dev-master'
sed -i 's~"vendor/autoload.php"~"tmp/vendor/autoload.php"~g' phpunit.xml.dist
