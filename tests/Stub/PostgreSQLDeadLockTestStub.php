<?php

declare(strict_types=1);

namespace Tests\Stub;

class PostgreSQLDeadLockTestStub extends BrokenTestBase implements BrokenTestInterface
{
    public const OUTPUT = 'SQLSTATE[40P01]: Deadlock detected: 7 ERROR:  deadlock detected';

    /**
     * @throws \Exception
     */
    public function testBrokenTest()
    {
        throw new \Exception(self::OUTPUT);
    }
}
