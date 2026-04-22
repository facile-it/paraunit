<?php

use ShipMonk\ComposerDependencyAnalyser\Config\Configuration;
use ShipMonk\ComposerDependencyAnalyser\Config\ErrorType;

$config = new Configuration();

$config->addPathToScan(__DIR__ . '/.php-cs-fixer.dist.php', true);
$config->addPathToScan(__DIR__ . '/rector.php', true);
$config->addPathToScan(__FILE__, true);
$config->enableAnalysisOfUnusedDevDependencies();
$config->ignoreErrorsOnPackages([
    'jangregor/phpstan-prophecy',
    'phpstan/extension-installer',
    'phpstan/phpstan',
    'phpstan/phpstan-phpunit',
    'phpunit/php-invoker', // for test timeouts
], [ErrorType::UNUSED_DEPENDENCY]);
$config->ignoreErrorsOnExtension('ext-pcntl', [ErrorType::DEV_DEPENDENCY_IN_PROD]);

return $config;
