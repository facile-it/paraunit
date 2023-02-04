<?php

declare(strict_types=1);

namespace Paraunit\Logs\TestHook;

use Paraunit\Logs\ValueObject\Test;
use Paraunit\Logs\ValueObject\TestStatus;
use PHPUnit\Event\Test\PhpunitWarningTriggered;
use PHPUnit\Event\Test\PhpunitWarningTriggeredSubscriber;
use PHPUnit\Event\Test\PhpWarningTriggered;
use PHPUnit\Event\Test\PhpWarningTriggeredSubscriber;
use PHPUnit\Event\Test\WarningTriggered;
use PHPUnit\Event\Test\WarningTriggeredSubscriber;

class TestWarning extends AbstractTestHook implements WarningTriggeredSubscriber, PhpWarningTriggeredSubscriber, PhpunitWarningTriggeredSubscriber
{
    public function notify(WarningTriggered|PhpWarningTriggered|PhpunitWarningTriggered $event): void
    {
        $this->write(TestStatus::WarningTriggered, Test::fromPHPUnitTest($event->test()), $event->message());
    }
}
