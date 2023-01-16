<?php

declare(strict_types=1);

namespace Tests\Stub;

class PassThenRetryTestStub extends BrokenTestBase implements BrokenTestInterface
{
    public function testPass(): void
    {
        $this->assertTrue(true);
    }

    public function testFail(): never
    {
        $this->fail();
    }

    public function testPass2(): void
    {
        $this->assertTrue(true);
    }

    public function testBrokenTest(): never
    {
        throw new \Exception(MySQLDeadLockTestStub::OUTPUT);
    }
}
