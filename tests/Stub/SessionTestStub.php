<?php

declare(strict_types=1);

namespace Tests\Stub;

use PHPUnit\Framework\TestCase;

class SessionTestStub extends TestCase
{
    private const SESSION_ID = '42';

    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();

        session_id(self::SESSION_ID);
        session_start();
    }

    protected function setUp(): void
    {
        session_id(self::SESSION_ID);
    }

    public function testOne(): void
    {
        $process = new StubbedParaunitProcess();

        $this->assertTrue($process->isTerminated());
    }

    public function testTwo(): void
    {
        $this->assertEquals(self::SESSION_ID, session_id());
    }

    public function testThree(): void
    {
        $this->assertTrue(true);
    }
}
