<?php

namespace Paraunit\Tests\Stub;

class DeadLockTestStub extends BrokenTestBase implements BrokenTestInterface
{
    /**
     * @throws \Exception
     */
    public function testBrokenTest()
    {
        throw new \Exception('SQLSTATE[HY000]: General error: 1205 Lock wait timeout exceeded; try restarting transaction');
    }
}
