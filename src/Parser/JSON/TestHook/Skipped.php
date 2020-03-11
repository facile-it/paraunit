<?php

declare(strict_types=1);

namespace Paraunit\Parser\JSON\TestHook;

use Paraunit\Parser\JSON\Log;
use PHPUnit\Runner\AfterSkippedTestHook;

class Skipped extends AbstractTestHook implements AfterSkippedTestHook
{
    public function executeAfterSkippedTest(string $test, string $message, float $time): void
    {
        $this->write(Log::STATUS_SKIPPED, null);
    }
}
