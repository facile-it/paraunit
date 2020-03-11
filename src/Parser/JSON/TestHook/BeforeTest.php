<?php

declare(strict_types=1);

namespace Paraunit\Parser\JSON\TestHook;

use PHPUnit\Runner\BeforeTestHook;

class BeforeTest extends AbstractTestHook implements BeforeTestHook
{
    public function executeBeforeTest(string $test): void
    {
        $this->write(self::STATUS_TEST_START, $test);
    }
}
