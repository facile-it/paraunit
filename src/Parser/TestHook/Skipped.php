<?php

declare(strict_types=1);

namespace Paraunit\Parser\TestHook;

use Paraunit\Parser\JSON\Log;
use PHPUnit\Event\Test\Skipped as PHPUnitSkipped;
use PHPUnit\Event\Test\SkippedSubscriber;

class Skipped extends AbstractTestHook implements SkippedSubscriber
{
    public function notify(PHPUnitSkipped $event): void
    {
        $this->write(Log::STATUS_SKIPPED, $event->test(), $event->message());
    }
}
