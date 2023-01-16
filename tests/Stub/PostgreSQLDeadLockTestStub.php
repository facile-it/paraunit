<?php

declare(strict_types=1);

namespace Tests\Stub;

class PostgreSQLDeadLockTestStub extends BrokenTestBase implements BrokenTestInterface
{
    final public const OUTPUT = 'SQLSTATE[40P01]: Deadlock detected: 7 ERROR:  deadlock detected';

    /**
     * @throws \Exception
     */
    public function testBrokenTest(): never
    {
        throw new \Exception(self::OUTPUT);
    }
}
