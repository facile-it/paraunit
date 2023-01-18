<?php

declare(strict_types=1);

namespace Paraunit\Logs\TestHook;

use Paraunit\Logs\ValueObject\Test;
use Paraunit\Logs\ValueObject\TestStatus;
use PHPUnit\Event\Test\Failed;
use PHPUnit\Event\Test\FailedSubscriber;

class Failure extends AbstractTestHook implements FailedSubscriber
{
    public function notify(Failed $event): void
    {
        $this->write(TestStatus::Failed, Test::fromPHPUnitTest($event->test()), $event->throwable()->message());
    }
}
