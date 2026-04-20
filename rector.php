<?php

declare(strict_types=1);

use Rector\Config\RectorConfig;
use Rector\Php83\Rector\ClassMethod\AddOverrideAttributeToOverriddenMethodsRector;
use Rector\PHPUnit\PHPUnit100\Rector\Class_\ParentTestClassConstructorRector;
use Rector\PHPUnit\Set\PHPUnitSetList;
use Rector\TypeDeclaration\Rector\ClassMethod\ReturnNeverTypeRector;

return RectorConfig::configure()
    ->withImportNames(
        importShortClasses: false
    )
    ->withParallel()
    ->withPaths([
        __FILE__,
        __DIR__ . '/src',
        __DIR__ . '/tests',
    ])
    ->withPhpSets()
    ->withPreparedSets(
        deadCode: true,
        codeQuality: true,
        typeDeclarations: true,  
    )
    ->withSets([
        PHPUnitSetList::COMPOSER_BASED,
    ])
    ->withSkipPath(
        __DIR__ . '/tests/Stub/*',
    )
    ->withSkip([
        AddOverrideAttributeToOverriddenMethodsRector::class,
        ParentTestClassConstructorRector::class,
        ReturnNeverTypeRector::class,
    ]);
