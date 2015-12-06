<?php

namespace Paraunit\Tests\Stub;

/**
 * Class MySQLLockTimeoutTestStub
 * @package Paraunit\Tests\Stub
 */
class MySQLLockTimeoutTestStub extends BrokenTestBase implements BrokenTestInterface
{
    const OUTPUT = 'SQLSTATE[HY000]: General error: 1205 Lock wait timeout exceeded; try restarting transaction';

    /**
     * @throws \Exception
     */
    public function testBrokenTest()
    {
        throw new \Exception(self::OUTPUT);
    }
}
