<?php

declare(strict_types=1);

namespace Paraunit\Parser\JSON\TestHook;

use PHPUnit\Runner\AfterLastTestHook;

class AfterLastTest extends AbstractTestHook implements AfterLastTestHook
{
    public function executeAfterLastTest(): void
    {
        $this->write(self::STATUS_AFTER_LAST_TEST, null);
    }
}
