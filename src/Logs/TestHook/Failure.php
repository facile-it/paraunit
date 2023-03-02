<?php

declare(strict_types=1);

namespace Paraunit\Logs\TestHook;

use Paraunit\Logs\ValueObject\LogStatus;
use Paraunit\Logs\ValueObject\Test;
use PHPUnit\Event\Test\Failed;
use PHPUnit\Event\Test\FailedSubscriber;

/**
 * @template-extends AbstractTestHook<Failed>
 */
class Failure extends AbstractTestHook implements FailedSubscriber
{
    public function notify(Failed $event): void
    {
        $this->write(LogStatus::Failed, Test::fromPHPUnitTest($event->test()), $this->createMessageFromThrowable($event->throwable()));
    }
}
