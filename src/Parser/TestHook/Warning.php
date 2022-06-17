<?php

declare(strict_types=1);

namespace Paraunit\Parser\TestHook;

use Paraunit\Parser\TestStatus;
use PHPUnit\Event\Test\PassedWithWarning;
use PHPUnit\Event\Test\PassedWithWarningSubscriber;

class Warning extends AbstractTestHook implements PassedWithWarningSubscriber
{
    public function notify(PassedWithWarning $event): void
    {
        $this->write(TestStatus::PassedWithWarning, $event->test(), $event->throwable()->message());
    }
}
