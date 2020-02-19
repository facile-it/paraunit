<?php

declare(strict_types=1);

namespace Paraunit\Parser\JSON\TestHook;

use PHPUnit\Runner\AfterTestFailureHook;

class Failure extends AbstractTestHook implements AfterTestFailureHook
{
    public function executeAfterTestFailure(string $test, string $message, float $time): void
    {
        $this->write(self::STATUS_FAILURE, $message, $time);
    }
}
