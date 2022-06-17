<?php

declare(strict_types=1);

namespace Paraunit\Parser\TestHook;

use Paraunit\Parser\ValueObject\TestStatus;
use PHPUnit\Event\Test\ConsideredRisky;
use PHPUnit\Event\Test\ConsideredRiskySubscriber;

class Risky extends AbstractTestHook implements ConsideredRiskySubscriber
{
    public function notify(ConsideredRisky $event): void
    {
        $this->write(TestStatus::ConsideredRisky, $event->test(), $event->throwable()->message());
    }
}
