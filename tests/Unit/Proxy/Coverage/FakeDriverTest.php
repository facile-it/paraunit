<?php

namespace Tests\Unit\Proxy\Coverage;

use Paraunit\Proxy\Coverage\FakeDriver;
use PHPUnit\Framework\TestCase;

class FakeDriverTest extends TestCase
{
    /**
     * @param string $method
     * @dataProvider methodNameProvider
     */
    public function testUnusableMethods(string $method): void
    {
        $driver = new FakeDriver();
        
        $this->expectException(\RuntimeException::class);
        
        $driver->$method();
    }

    public function methodNameProvider()
    {
        yield ['start'];
        yield ['stop'];
    }
}
