<?php

declare(strict_types=1);

namespace Paraunit\Parser\TestHook;

use Paraunit\Parser\JSON\Log;
use PHPUnit\Event\Test\PassedWithWarning;
use PHPUnit\Event\Test\PassedWithWarningSubscriber;

class Warning extends AbstractTestHook implements PassedWithWarningSubscriber
{
    public function notify(PassedWithWarning $event): void
    {
        $this->write(Log::STATUS_WARNING, $event->test(), $event->throwable()->message());
    }
}
