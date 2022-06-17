<?php

declare(strict_types=1);

namespace Paraunit\Parser\TestHook;

use Paraunit\Parser\DTO\TestStatus;
use PHPUnit\Event\Test\Finished as FinishedEvent;
use PHPUnit\Event\Test\FinishedSubscriber;

class Finished extends AbstractTestHook implements FinishedSubscriber
{
    public function notify(FinishedEvent $event): void
    {
        $this->write(TestStatus::Finished, $event->test(), null);
    }
}
