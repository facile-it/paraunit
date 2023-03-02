<?php

declare(strict_types=1);

namespace Paraunit\Logs\TestHook;

use Paraunit\Logs\ValueObject\LogStatus;
use Paraunit\Logs\ValueObject\Test;
use PHPUnit\Event\Test\PhpunitDeprecationTriggered;
use PHPUnit\Event\Test\PhpunitDeprecationTriggeredSubscriber;

/**
 * @template-extends AbstractTestHook<PhpunitDeprecationTriggered>
 */
class PhpUnitDeprecation extends AbstractTestHook implements PhpunitDeprecationTriggeredSubscriber
{
    public function notify(PhpunitDeprecationTriggered $event): void
    {
        $this->write(LogStatus::Deprecation, Test::fromPHPUnitTest($event->test()), $event->message());
    }
}
