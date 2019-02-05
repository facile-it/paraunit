<?php

declare(strict_types=1);

namespace Tests\Stub;

use PHPUnit\Framework\TestCase;

class ThreeGreenTestStub extends TestCase
{
    public function testGreenOne(): void
    {
        $process = new StubbedParaunitProcess();

        $this->assertTrue($process->isTerminated());
    }

    public function testGreenTwo(): void
    {
        $this->assertTrue(true);
    }

    public function testGreenThree(): void
    {
        $this->assertTrue(true);
    }
}
