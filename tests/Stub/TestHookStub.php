<?php

declare(strict_types=1);

namespace Tests\Stub;

use Paraunit\Logs\TestHook\AbstractTestHook;

class TestHookStub extends AbstractTestHook
{
    public static function reset(): void
    {
        self::$logFile = null;
    }
}
