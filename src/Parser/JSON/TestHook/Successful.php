<?php

declare(strict_types=1);

namespace Paraunit\Parser\JSON\TestHook;

use Paraunit\Parser\JSON\Log;
use PHPUnit\Runner\AfterSuccessfulTestHook;

class Successful extends AbstractTestHook implements AfterSuccessfulTestHook
{
    public function executeAfterSuccessfulTest(string $test, float $time): void
    {
        $this->write(Log::STATUS_SUCCESSFUL, null);
    }
}
