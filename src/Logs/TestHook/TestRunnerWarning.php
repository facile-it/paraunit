<?php

declare(strict_types=1);

namespace Paraunit\Logs\TestHook;

use Paraunit\Logs\ValueObject\LogStatus;
use Paraunit\Logs\ValueObject\Test;
use PHPUnit\Event\TestRunner\WarningTriggered;
use PHPUnit\Event\TestRunner\WarningTriggeredSubscriber;

class TestRunnerWarning extends AbstractTestHook implements WarningTriggeredSubscriber
{
    public function notify(WarningTriggered $event): void
    {
        // TODO - how can we reproduce and test this situation?
        $this->write(LogStatus::WarningTriggered, Test::unknown(), $event->message());
    }
}
