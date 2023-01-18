<?php

declare(strict_types=1);

namespace Paraunit\Logs\TestHook;

use Paraunit\Logs\ValueObject\Test;
use Paraunit\Logs\ValueObject\TestStatus;
use PHPUnit\Event\Test\WarningTriggered;
use PHPUnit\Event\Test\WarningTriggeredSubscriber;

class Warning extends AbstractTestHook implements WarningTriggeredSubscriber
{
    public function notify(WarningTriggered $event): void
    {
        // TODO - what about PHPUnitWarningTriggered?
        $this->write(TestStatus::WarningTriggered, Test::fromPHPUnitTest($event->test()), $event->message());
    }
}
