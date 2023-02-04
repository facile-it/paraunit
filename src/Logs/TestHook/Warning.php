<?php

declare(strict_types=1);

namespace Paraunit\Logs\TestHook;

use Paraunit\Logs\ValueObject\Test;
use Paraunit\Logs\ValueObject\TestStatus;
use PHPUnit\Event\TestRunner\WarningTriggered;
use PHPUnit\Event\TestRunner\WarningTriggeredSubscriber;

class Warning extends AbstractTestHook implements WarningTriggeredSubscriber
{
    public function notify(WarningTriggered $event): void
    {
        // TODO - how can we reproduce and test this situation?
        $this->write(TestStatus::WarningTriggered, Test::unknown(), $event->message());
    }
}
