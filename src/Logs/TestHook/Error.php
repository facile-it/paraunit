<?php

declare(strict_types=1);

namespace Paraunit\Logs\TestHook;

use Paraunit\Logs\ValueObject\LogStatus;
use Paraunit\Logs\ValueObject\Test;
use PHPUnit\Event\Test\Errored;
use PHPUnit\Event\Test\ErroredSubscriber;

/**
 * @template-extends AbstractTestHook<Errored>
 */
class Error extends AbstractTestHook implements ErroredSubscriber
{
    public function notify(Errored $event): void
    {
        $this->write(LogStatus::Errored, Test::fromPHPUnitTest($event->test()), $this->createMessageFromThrowable($event->throwable()));
    }
}
