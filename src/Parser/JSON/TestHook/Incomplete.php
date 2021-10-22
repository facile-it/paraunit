<?php

declare(strict_types=1);

namespace Paraunit\Parser\JSON\TestHook;

use Paraunit\Parser\JSON\Log;
use PHPUnit\Event\Test\Aborted;
use PHPUnit\Event\Test\AbortedSubscriber;

class Incomplete extends AbstractTestHook implements AbortedSubscriber
{
    public function notify(Aborted $event): void
    {
        $this->write(Log::STATUS_INCOMPLETE, $event->test(), $event->throwable()->message());
    }
}
