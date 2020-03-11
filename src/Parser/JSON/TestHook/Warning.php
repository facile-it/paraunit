<?php

declare(strict_types=1);

namespace Paraunit\Parser\JSON\TestHook;

use Paraunit\Parser\JSON\Log;
use PHPUnit\Runner\AfterTestWarningHook;

class Warning extends AbstractTestHook implements AfterTestWarningHook
{
    public function executeAfterTestWarning(string $test, string $message, float $time): void
    {
        $this->write(Log::STATUS_WARNING, $message);
    }
}
