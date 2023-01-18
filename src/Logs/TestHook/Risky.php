<?php

declare(strict_types=1);

namespace Paraunit\Logs\TestHook;

use Paraunit\Logs\ValueObject\Test;
use Paraunit\Logs\ValueObject\TestStatus;
use PHPUnit\Event\Test\ConsideredRisky;
use PHPUnit\Event\Test\ConsideredRiskySubscriber;

class Risky extends AbstractTestHook implements ConsideredRiskySubscriber
{
    public function notify(ConsideredRisky $event): void
    {
        $this->write(TestStatus::ConsideredRisky, Test::fromPHPUnitTest($event->test()), $event->message());
    }
}
