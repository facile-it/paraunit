<?php

declare(strict_types=1);

namespace Paraunit\Logs\TestHook;

use Paraunit\Logs\ValueObject\LogStatus;
use Paraunit\Logs\ValueObject\Test;
use PHPUnit\Event\Test\PhpDeprecationTriggered;
use PHPUnit\Event\Test\PhpDeprecationTriggeredSubscriber;

/**
 * @template-extends AbstractTestHook<PhpDeprecationTriggered>
 */
class PhpDeprecation extends AbstractTestHook implements PhpDeprecationTriggeredSubscriber
{
    public function notify(PhpDeprecationTriggered $event): void
    {
        $this->write(LogStatus::Deprecation, Test::fromPHPUnitTest($event->test()), $event->message());
    }
}
