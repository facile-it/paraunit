<?php

declare(strict_types=1);

namespace Paraunit\Parser\JSON\TestHook;

use Paraunit\Parser\JSON\Log;
use PHPUnit\Event\Test\BeforeTestMethodCalled;
use PHPUnit\Event\Test\BeforeTestMethodCalledSubscriber;
use PHPUnit\Runner\BeforeTestHook;

class BeforeTest extends AbstractTestHook implements BeforeTestMethodCalledSubscriber
{
    public function notify(BeforeTestMethodCalled $event): void
    {
        $this->write(Log::STATUS_TEST_START, $event->testClassName(), null);
    }
}
