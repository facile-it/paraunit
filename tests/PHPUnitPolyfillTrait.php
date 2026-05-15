<?php

declare(strict_types=1);

namespace Tests;

use PHPUnit\Event\Code\TestMethod;
use PHPUnit\Event\Code\TestMethodBuilder;
use PHPUnit\Event\Code\Throwable;
use PHPUnit\Event\Code\ThrowableBuilder;

trait PHPUnitPolyfillTrait
{
    protected function createPHPUnitTestMethod(): TestMethod
    {
        return TestMethodBuilder::fromTestCase($this);
    }

    protected function createPHPUnitThrowable(\Throwable $throwable): Throwable
    {
        return ThrowableBuilder::from($throwable);
    }
}
