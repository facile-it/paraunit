#!/usr/bin/env php
<?php
Phar::mapPhar();
define('PARAUNIT_PHAR_FILE', __FILE__);
require 'phar://paraunit.phar/vendor/autoload.php';
if (isset($argv[1]) && $argv[1] === 'phpunit') {
    // Do not run Paraunit itself, actually run the embedded PHPUnit
    // This is required for running PHPUnit parallel subprocesses
    array_shift($argv);
    define('__PHPUNIT_PHAR__', str_replace(DIRECTORY_SEPARATOR, '/', __FILE__));
    define('__PHPUNIT_PHAR_ROOT__', 'phar://paraunit.phar');
    PHPUnit_TextUI_Command::main();
}
require 'phar://paraunit.phar/src/Paraunit/Bin/run-paraunit.inc.php';
__HALT_COMPILER();