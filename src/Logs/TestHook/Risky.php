<?php

declare(strict_types=1);

namespace Paraunit\Logs\TestHook;

use Paraunit\Logs\ValueObject\LogStatus;
use Paraunit\Logs\ValueObject\Test;
use PHPUnit\Event\Test\ConsideredRisky;
use PHPUnit\Event\Test\ConsideredRiskySubscriber;

class Risky extends AbstractTestHook implements ConsideredRiskySubscriber
{
    public function notify(ConsideredRisky $event): void
    {
        $this->write(LogStatus::ConsideredRisky, Test::fromPHPUnitTest($event->test()), $event->message());
    }
}
