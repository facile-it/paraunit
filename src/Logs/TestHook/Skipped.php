<?php

declare(strict_types=1);

namespace Paraunit\Logs\TestHook;

use Paraunit\Logs\ValueObject\Test;
use Paraunit\Logs\ValueObject\TestStatus;
use PHPUnit\Event\Test\Skipped as PHPUnitSkipped;
use PHPUnit\Event\Test\SkippedSubscriber;

class Skipped extends AbstractTestHook implements SkippedSubscriber
{
    public function notify(PHPUnitSkipped $event): void
    {
        $this->write(TestStatus::Skipped, Test::fromPHPUnitTest($event->test()), $event->message());
    }
}
