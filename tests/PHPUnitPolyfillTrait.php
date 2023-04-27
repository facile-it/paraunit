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
        if (class_exists(TestMethodBuilder::class)) {
            return TestMethodBuilder::fromTestCase($this);
        }

        if (method_exists(TestMethod::class, 'fromTestCase')) {
            return TestMethod::fromTestCase($this);
        }

        throw new \RuntimeException('Cannot create PHPUnit TestMethod class');
    }

    protected function createPHPUnitThrowable(\Throwable $throwable): Throwable
    {
        if (class_exists(ThrowableBuilder::class)) {
            return ThrowableBuilder::from($throwable);
        }

        if (method_exists(Throwable::class, 'from')) {
            return Throwable::from($throwable);
        }

        throw new \RuntimeException('Cannot create PHPUnit Throwable class');
    }
}
