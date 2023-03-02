<?php

declare(strict_types=1);

namespace Paraunit\Logs\TestHook;

use Paraunit\Logs\ValueObject\LogStatus;
use Paraunit\Logs\ValueObject\Test;
use PHPUnit\Event\Test\DeprecationTriggered;
use PHPUnit\Event\Test\DeprecationTriggeredSubscriber;

/**
 * @template-extends AbstractTestHook<DeprecationTriggered>
 */
class Deprecation extends AbstractTestHook implements DeprecationTriggeredSubscriber
{
    public function notify(DeprecationTriggered $event): void
    {
        $this->write(LogStatus::Deprecation, Test::fromPHPUnitTest($event->test()), $event->message());
    }
}
