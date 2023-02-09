<?php

declare(strict_types=1);

namespace Tests\Unit\Proxy\Coverage;

use Paraunit\Proxy\Coverage\FakeDriver;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class FakeDriverTest extends TestCase
{
    public function testName(): void
    {
        $driver = new FakeDriver();

        $this->assertStringContainsString('Fake', $driver->nameAndVersion());
    }

    #[DataProvider('methodNameProvider')]
    public function testUnusableMethods(string $method): void
    {
        $driver = new FakeDriver();

        $this->expectException(\RuntimeException::class);

        $driver->$method();
    }

    /**
     * @return \Generator<string[]>
     */
    public static function methodNameProvider(): \Generator
    {
        yield ['start'];
        yield ['stop'];
    }
}
