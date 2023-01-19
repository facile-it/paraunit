<?php

declare(strict_types=1);

namespace Paraunit\Logs\TestHook;

use Paraunit\Logs\ValueObject\Test;
use Paraunit\Logs\ValueObject\TestStatus;
use PHPUnit\Event\Test\Errored;
use PHPUnit\Event\Test\ErroredSubscriber;

class Error extends AbstractTestHook implements ErroredSubscriber
{
    public function notify(Errored $event): void
    {
        $this->write(TestStatus::Errored, Test::fromPHPUnitTest($event->test()), $this->createMessageFromThrowable($event->throwable()));
    }
}
