<?php

declare(strict_types=1);

namespace Paraunit\Logs\TestHook;

use Paraunit\Logs\ValueObject\LogStatus;
use Paraunit\Logs\ValueObject\Test;
use PHPUnit\Event\Test\MarkedIncomplete;
use PHPUnit\Event\Test\MarkedIncompleteSubscriber;

class Incomplete extends AbstractTestHook implements MarkedIncompleteSubscriber
{
    public function notify(MarkedIncomplete $event): void
    {
        $this->write(LogStatus::MarkedIncomplete, Test::fromPHPUnitTest($event->test()), $event->throwable()->message());
    }
}
