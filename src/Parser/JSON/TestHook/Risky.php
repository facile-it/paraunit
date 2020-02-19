<?php

declare(strict_types=1);

namespace Paraunit\Parser\JSON\TestHook;

use PHPUnit\Runner\AfterRiskyTestHook;

class Risky extends AbstractTestHook implements AfterRiskyTestHook
{
    public function executeAfterRiskyTest(string $test, string $message, float $time): void
    {
        $this->write(self::STATUS_RISKY, $message, $time);
    }
}
