<?php

if ( ! ini_get('date.timezone') && ! date_default_timezone_get()) {
    date_default_timezone_set('UTC');
}

// HOTFIX -- needed to fool the Symfony's WebTestCase
$_SERVER['argv'][0] = 'phpunit';

Paraunit\Configuration\Paraunit::buildContainer()
    ->get('paraunit.application')
    ->run();
