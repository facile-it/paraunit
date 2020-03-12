<?php

declare(strict_types=1);

namespace Paraunit\Parser\JSON\TestHook;

use Paraunit\Parser\JSON\Log;
use PHPUnit\Runner\BeforeTestHook;

class BeforeTest extends AbstractTestHook implements BeforeTestHook
{
    public function executeBeforeTest(string $test): void
    {
        $this->write(Log::STATUS_TEST_START, $test, null);
    }
}
