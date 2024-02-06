<?php

declare(strict_types=1);

namespace Tests\Unit\Logs\ValueObject;

use Paraunit\Logs\ValueObject\Test;
use PHPUnit\Event\Code\TestMethodBuilder;
use Tests\BaseUnitTestCase;

class TestTest extends BaseUnitTestCase
{
    public function testSerialization(): void
    {
        $test = new Test('Foo');

        $encoded = json_encode($test, JSON_THROW_ON_ERROR);
        $decoded = Test::deserialize(json_decode($encoded, true, JSON_THROW_ON_ERROR, JSON_THROW_ON_ERROR));

        $this->assertEquals($test, $decoded);
    }

    public function testFromPHPUnitTest(): void
    {
        $phpunitTest = TestMethodBuilder::fromTestCase($this);

        $test = Test::fromPHPUnitTest($phpunitTest);

        $this->assertSame(__METHOD__, $test->name);
    }
}
