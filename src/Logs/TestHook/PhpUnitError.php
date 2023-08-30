<?php

declare(strict_types=1);

namespace Paraunit\Logs\TestHook;

use Paraunit\Logs\ValueObject\LogStatus;
use Paraunit\Logs\ValueObject\Test;
use PHPUnit\Event\Test\PhpunitErrorTriggered;
use PHPUnit\Event\Test\PhpunitErrorTriggeredSubscriber;

/**
 * @template-extends AbstractTestHook<PhpunitErrorTriggered>
 */
class PhpUnitError extends AbstractTestHook implements PhpunitErrorTriggeredSubscriber
{
    public function notify(PhpunitErrorTriggered $event): void
    {
        $this->write(LogStatus::ErrorTriggered, Test::fromPHPUnitTest($event->test()), $event->message());
    }
}
