<?php

declare(strict_types=1);

namespace Paraunit\Parser\JSON\TestHook;

use Paraunit\Parser\JSON\Log;
use PHPUnit\Event\Test\ConsideredRisky;
use PHPUnit\Event\Test\ConsideredRiskySubscriber;

class Risky extends AbstractTestHook implements ConsideredRiskySubscriber
{
    public function notify(ConsideredRisky $event): void
    {
        $this->write(Log::STATUS_RISKY, $event->test()->name(), $event->throwable()->message());
    }
}
