<?php

use PHPUnit\Event\Code\IssueTrigger\IssueTrigger;
use PHPUnit\Event\Telemetry\SystemGarbageCollectorStatusProvider;
use ShipMonk\ComposerDependencyAnalyser\Config\Configuration;
use ShipMonk\ComposerDependencyAnalyser\Config\ErrorType;

$config = new Configuration();

if (class_exists(SystemGarbageCollectorStatusProvider::class)) {
    /**
     * Compatibility with PHPUnit ^10.1 || ^11
     * @see \Tests\Unit\Logs\TestHook\AbstractTestHookTestCase::createGarbageCollectorStatus()
     */
    $config->ignoreUnknownClassesRegex('/PHPUnit\\\Event\\\Telemetry\\\Php81|3GarbageCollectorStatusProvider/');
} else {
    /**
     * Compatibility with PHPUnit >= 12.0.0
     * @see \Tests\Unit\Logs\TestHook\AbstractTestHookTestCase::createGarbageCollectorStatus()
     */
    $config->ignoreUnknownClassesRegex('/PHPUnit\\\Event\\\Telemetry\\\SystemGarbageCollectorStatusProvider/');
}

if (! class_exists(IssueTrigger::class)) {
    /**
     * Compatibility with PHPUnit >= 11.0.0
     * @see \Tests\Unit\Logs\TestHook\DeprecationTest
     * @see \Tests\Unit\Logs\TestHook\PhpDeprecationTest
     */
    $config->ignoreUnknownClassesRegex('/PHPUnit\\\Event\\\Code\\\IssueTrigger\\\Code/');
    $config->ignoreUnknownClassesRegex('/PHPUnit\\\Event\\\Code\\\IssueTrigger\\\IssueTrigger/');
}

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
    'psalm/plugin-phpunit',
    'psalm/plugin-symfony',
    'vimeo/psalm',
], [ErrorType::UNUSED_DEPENDENCY]);

return $config;
