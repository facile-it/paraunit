<?php

declare(strict_types=1);

namespace Tests\Unit\Logs\ValueObject;

use Paraunit\Logs\ValueObject\Test;
use Paraunit\Logs\ValueObject\TestMethod;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\BaseUnitTestCase;

class TestMethodTest extends BaseUnitTestCase
{
    public function testSerialization(): void
    {
        $test = new TestMethod('Foo', 'method');

        $encoded = json_encode($test, JSON_THROW_ON_ERROR);
        $decoded = Test::deserialize(json_decode($encoded, true, JSON_THROW_ON_ERROR, JSON_THROW_ON_ERROR));

        $this->assertEquals($test, $decoded);

        $decoded = TestMethod::deserialize(json_decode($encoded, true, JSON_THROW_ON_ERROR, JSON_THROW_ON_ERROR));

        $this->assertEquals($test, $decoded);
    }

    public function testFromPHPUnitTest(): void
    {
        $phpunitTest = $this->createPHPUnitTestMethod();

        $test = Test::fromPHPUnitTest($phpunitTest);

        $this->assertInstanceOf(TestMethod::class, $test);
        $this->assertSame(self::class, $test->className);
        $this->assertSame('testFromPHPUnitTest', $test->methodName);
        $this->assertSame(self::class . '::testFromPHPUnitTest', $test->name);
    }

    #[DataProvider('dataSetProvider')]
    public function testWithDataProvider(string $expectedEnding): void
    {
        $phpunitTest = $this->createPHPUnitTestMethod();

        $test = Test::fromPHPUnitTest($phpunitTest);

        $this->assertInstanceOf(TestMethod::class, $test);
        $this->assertSame(self::class, $test->className);
        $this->assertSame('testWithDataProvider', $test->methodName);
        $this->assertSame(self::class . '::testWithDataProvider ' . $expectedEnding, $test->name);
    }

    /**
     * @return array{string}[]
     */
    public static function dataSetProvider(): array
    {
        return [
            0 => ['with data set #0'],
            'foo' => ['with data set "foo"'],
        ];
    }
}
