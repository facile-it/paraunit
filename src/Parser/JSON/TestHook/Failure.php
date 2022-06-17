<?php

declare(strict_types=1);

namespace Paraunit\Parser\JSON\TestHook;

use Paraunit\Parser\JSON\Log;
use PHPUnit\Event\Test\Failed;
use PHPUnit\Event\Test\FailedSubscriber;

class Failure extends AbstractTestHook implements FailedSubscriber
{
    public function notify(Failed $event): void
    {
        $this->write(Log::STATUS_FAILURE, $event->test(), $event->throwable()->message());
    }
}
