<?php

declare(strict_types=1);

use Rector\Config\RectorConfig;
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
        PHPUnitSetList::PHPUNIT_100,
    ])
    ->withSkipPath(
        __DIR__ . '/tests/Stub/ParseErrorTestStub.php',
    )
    ->withSkip([
        ReturnNeverTypeRector::class,
    ]);
