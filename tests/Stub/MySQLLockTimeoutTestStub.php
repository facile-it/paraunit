<?php

declare(strict_types=1);

namespace Tests\Stub;

class MySQLLockTimeoutTestStub extends BrokenTestBase implements BrokenTestInterface
{
    final public const OUTPUT = 'SQLSTATE[HY000]: General error: 1205 Lock wait timeout exceeded; try restarting transaction';

    /**
     * @throws \Exception
     */
    public function testBrokenTest(): never
    {
        throw new \Exception(self::OUTPUT);
    }
}
