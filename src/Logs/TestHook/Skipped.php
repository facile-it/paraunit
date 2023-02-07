<?php

declare(strict_types=1);

namespace Paraunit\Logs\TestHook;

use Paraunit\Logs\ValueObject\LogStatus;
use Paraunit\Logs\ValueObject\Test;
use PHPUnit\Event\Test\Skipped as PHPUnitSkipped;
use PHPUnit\Event\Test\SkippedSubscriber;

class Skipped extends AbstractTestHook implements SkippedSubscriber
{
    public function notify(PHPUnitSkipped $event): void
    {
        $this->write(LogStatus::Skipped, Test::fromPHPUnitTest($event->test()), $event->message());
    }
}
