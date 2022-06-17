<?php

declare(strict_types=1);

namespace Paraunit\Parser\TestHook;

use Paraunit\Parser\DTO\TestStatus;
use PHPUnit\Event\Test\MarkedIncomplete;
use PHPUnit\Event\Test\MarkedIncompleteSubscriber;

class Incomplete extends AbstractTestHook implements MarkedIncompleteSubscriber
{
    public function notify(MarkedIncomplete $event): void
    {
        $this->write(TestStatus::MarkedIncomplete, $event->test(), $event->throwable()->message());
    }
}
