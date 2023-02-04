<?php

declare(strict_types=1);

namespace Paraunit\Logs\TestHook;

use Paraunit\Logs\ValueObject\LogStatus;
use Paraunit\Logs\ValueObject\Test;
use PHPUnit\Event\Test\Passed as PassedEvent;
use PHPUnit\Event\Test\PassedSubscriber;

class Passed extends AbstractTestHook implements PassedSubscriber
{
    public function notify(PassedEvent $event): void
    {
        $this->write(LogStatus::Passed, Test::fromPHPUnitTest($event->test()), null);
    }
}
