<?php

declare(strict_types=1);

namespace Paraunit\Parser\JSON\TestHook;

use PHPUnit\Runner\AfterSuccessfulTestHook;

class Successful extends AbstractTestHook implements AfterSuccessfulTestHook
{
    public function executeAfterSuccessfulTest(string $test, float $time): void
    {
        $this->write(self::STATUS_SUCCESSFUL, null);
    }
}
