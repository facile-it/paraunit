<?php

declare(strict_types=1);

namespace Paraunit\Parser\TestHook;

use Paraunit\Parser\DTO\TestStatus;
use PHPUnit\Event\Test\Passed as PassedEvent;
use PHPUnit\Event\Test\PassedSubscriber;

class Passed extends AbstractTestHook implements PassedSubscriber
{
    public function notify(PassedEvent $event): void
    {
        $this->write(TestStatus::Passed, $event->test(), null);
    }
}
