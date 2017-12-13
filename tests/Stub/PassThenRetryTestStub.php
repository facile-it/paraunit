<?php

declare(strict_types=1);

namespace Tests\Stub;

class PassThenRetryTestStub extends BrokenTestBase implements BrokenTestInterface
{
    public function testPass()
    {
        $this->assertTrue(true);
    }

    public function testFail()
    {
        $this->fail();
    }

    public function testPass2()
    {
        $this->assertTrue(true);
    }

    public function testBrokenTest()
    {
        throw new \Exception(MySQLDeadLockTestStub::OUTPUT);
    }
}
