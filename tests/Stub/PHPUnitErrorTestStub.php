<?php

declare(strict_types=1);

namespace Tests\Stub;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class PHPUnitErrorTestStub extends TestCase
{
    public function testToAvoidWarnings(): void
    {
        $this->assertTrue(true);
    }

    #[DataProvider('emptyDataProvider')]
    public function testWhichTriggersPHPUnitError(): void
    {
        $this->assertTrue(true);
    }

    public static function emptyDataProvider(): array
    {
        return [];
    }
}
