<?php

declare(strict_types=1);

namespace Paraunit\Logs\TestHook;

use Paraunit\Logs\ValueObject\Test;
use Paraunit\Logs\ValueObject\TestStatus;
use PHPUnit\Event\Test\DeprecationTriggered;
use PHPUnit\Event\Test\DeprecationTriggeredSubscriber;
use PHPUnit\Event\Test\PhpDeprecationTriggered;
use PHPUnit\Event\Test\PhpDeprecationTriggeredSubscriber;
use PHPUnit\Event\Test\PhpunitDeprecationTriggered;
use PHPUnit\Event\Test\PhpunitDeprecationTriggeredSubscriber;

class Deprecation extends AbstractTestHook implements DeprecationTriggeredSubscriber, PhpDeprecationTriggeredSubscriber, PhpunitDeprecationTriggeredSubscriber
{
    public function notify(DeprecationTriggered|PhpDeprecationTriggered|PhpunitDeprecationTriggered $event): void
    {
        $this->write(TestStatus::Deprecation, Test::fromPHPUnitTest($event->test()), $event->message());
    }
}
