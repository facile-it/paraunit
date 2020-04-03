<?php

declare(strict_types=1);

namespace Paraunit\Parser\JSON\TestHook;

use Paraunit\Parser\JSON\Log;
use PHPUnit\Runner\AfterIncompleteTestHook;

class Incomplete extends AbstractTestHook implements AfterIncompleteTestHook
{
    public function executeAfterIncompleteTest(string $test, string $message, float $time): void
    {
        $this->write(Log::STATUS_INCOMPLETE, $test, $message);
    }
}
