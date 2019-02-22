<?php

declare(strict_types=1);

namespace Tests\Stub;

class MySQLSavePointMissingTestStub extends BrokenTestBase implements BrokenTestInterface
{
    public const OUTPUT = 'SQLSTATE[42000]: Syntax error or access violation: 1305 SAVEPOINT DOCTRINE2_SAVEPOINT_2 does not exist';

    /**
     * @throws \Exception
     */
    public function testBrokenTest()
    {
        throw new \Exception(self::OUTPUT);
    }
}
