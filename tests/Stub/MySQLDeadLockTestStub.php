<?php

declare(strict_types=1);

namespace Tests\Stub;

class MySQLDeadLockTestStub extends BrokenTestBase implements BrokenTestInterface
{
    final public const OUTPUT = 'SQLSTATE[HY000]: General error: Deadlock found; try restarting transaction';

    /**
     * @throws \Exception
     */
    public function testBrokenTest(): never
    {
        throw new \Exception(self::OUTPUT);
    }
}
