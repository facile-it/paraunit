<?php

declare(strict_types=1);

namespace Paraunit\Logs\TestHook;

use Paraunit\Logs\ValueObject\LogStatus;
use Paraunit\Logs\ValueObject\Test;
use PHPUnit\Event\Test\PhpWarningTriggered;
use PHPUnit\Event\Test\PhpWarningTriggeredSubscriber;

/**
 * @template-extends AbstractTestHook<PhpWarningTriggered>
 */
class PhpWarning extends AbstractTestHook implements PhpWarningTriggeredSubscriber
{
    public function notify(PhpWarningTriggered $event): void
    {
        $this->write(LogStatus::WarningTriggered, Test::fromPHPUnitTest($event->test()), $event->message());
    }
}
