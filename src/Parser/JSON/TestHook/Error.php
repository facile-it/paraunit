<?php

declare(strict_types=1);

namespace Paraunit\Parser\JSON\TestHook;

use PHPUnit\Runner\AfterTestErrorHook;

class Error extends AbstractTestHook implements AfterTestErrorHook
{
    public function executeAfterTestError(string $test, string $message, float $time): void
    {
        $this->write(self::STATUS_ERROR, $message, $time);
    }
}
