<?php

use PHPUnit\Event\Code\IssueTrigger\IssueTrigger;
use PHPUnit\Event\Telemetry\SystemGarbageCollectorStatusProvider;
use ShipMonk\ComposerDependencyAnalyser\Config\Configuration;

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

return $config;
