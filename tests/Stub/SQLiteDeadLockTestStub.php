<?php
declare(strict_types=1);

namespace Tests\Stub;

/**
 * Class SQLiteDeadLockTestStub
 * @package Tests\Stub
 */
class SQLiteDeadLockTestStub extends BrokenTestBase implements BrokenTestInterface
{
    const OUTPUT = 'SQLSTATE[HY000]: General error: 5 database is locked (SQL: insert into "wallets" ("name", "user_id", "updated_at", "created_at") values (test_wallet, 1, 2015-11-25 21:07:38, 2015-11-25 21:07:38))';

    /**
     * @throws \Exception
     */
    public function testBrokenTest()
    {
        throw new \Exception(self::OUTPUT);
    }
}
