<?php

declare(strict_types=1);

namespace Tests\Stub;

/**
 * Class MySQLLockTimeoutTestStub
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
