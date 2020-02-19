<?php

declare(strict_types=1);

namespace Paraunit\Parser\JSON\TestHook;

use PHPUnit\Runner\AfterSkippedTestHook;

class Skipped extends AbstractTestHook implements AfterSkippedTestHook
{
    public function executeAfterSkippedTest(string $test, string $message, float $time): void
    {
        $this->write(self::STATUS_SKIPPED, null, $time);
    }
}
