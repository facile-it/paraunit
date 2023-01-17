<?php

declare(strict_types=1);

namespace Paraunit\Logs\ValueObject;

use PHPUnit\Event\Code\Test as PHPUnitTest;
use PHPUnit\Event\Code\TestMethod;

class Test
{
    public function __construct(public readonly string $name)
    {
    }

    public static function fromPHPUnitTest(PHPUnitTest $test): self
    {
        $name = $test instanceof TestMethod
            ? $test->nameWithClass()
            : $test->name();

        return new self($name);
    }

    public static function unknown(): self
    {
        return new self('[UNKNOWN]');
    }
}
