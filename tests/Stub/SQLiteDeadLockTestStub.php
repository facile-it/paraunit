<?php

declare(strict_types=1);

namespace Tests\Stub;

class SQLiteDeadLockTestStub extends BrokenTestBase implements BrokenTestInterface
{
    final public const OUTPUT = 'SQLSTATE[HY000]: General error: 5 database is locked (SQL: insert into "wallets" ("name", "user_id", "updated_at", "created_at") values (test_wallet, 1, 2015-11-25 21:07:38, 2015-11-25 21:07:38))';

    /**
     * @throws \Exception
     */
    public function testBrokenTest(): never
    {
        throw new \Exception(self::OUTPUT);
    }
}
