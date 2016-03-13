<?php

namespace Tests\Stub;

/**
 * Class MySQLDeadLockTestStub
 * @package Tests\Stub
 */
class MySQLDeadLockTestStub extends BrokenTestBase implements BrokenTestInterface
{
    const OUTPUT = 'SQLSTATE[HY000]: General error: Deadlock found; try restarting transaction';

    /**
     * @throws \Exception
     */
    public function testBrokenTest()
    {
        throw new \Exception(self::OUTPUT);
    }
}
