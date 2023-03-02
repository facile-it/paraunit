<?php

declare(strict_types=1);

namespace Paraunit\Logs\TestHook;

use Paraunit\Logs\ValueObject\LogStatus;
use Paraunit\Logs\ValueObject\Test;
use PHPUnit\Event\Test\PhpunitWarningTriggered;
use PHPUnit\Event\Test\PhpunitWarningTriggeredSubscriber;

/**
 * @template-extends AbstractTestHook<PhpunitWarningTriggered>
 */
class PhpUnitWarning extends AbstractTestHook implements PhpunitWarningTriggeredSubscriber
{
    public function notify(PhpunitWarningTriggered $event): void
    {
        $this->write(LogStatus::WarningTriggered, Test::fromPHPUnitTest($event->test()), $event->message());
    }
}
