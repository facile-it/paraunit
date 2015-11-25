<?php

namespace Paraunit\Tests\Stub;

/**
 * Class MySQLDeadLockTestStub
 * @package Paraunit\Tests\Stub
 */
class MySQLDeadLockTestStub extends BrokenTestBase implements BrokenTestInterface
{
    /**
     * @throws \Exception
     */
    public function testBrokenTest()
    {
        throw new \Exception('SQLSTATE[HY000]: General error: 5 database is locked (SQL: insert into "wallets" ("name", "user_id", "updated_at", "created_at") values (test_wallet, 1, 2015-11-25 21:07:38, 2015-11-25 21:07:38))');
    }
}
