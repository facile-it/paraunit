<?php

declare(strict_types=1);

namespace Paraunit\Parser\JSON\TestHook;

use Paraunit\Parser\JSON\Log;
use PHPUnit\Runner\AfterLastTestHook;

class AfterLastTest extends AbstractTestHook implements AfterLastTestHook
{
    public function executeAfterLastTest(): void
    {
        $this->write(Log::STATUS_AFTER_LAST_TEST, null, null);
    }
}
