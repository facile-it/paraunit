<?php

declare(strict_types=1);

namespace Paraunit\Logs\TestHook;

use Paraunit\Logs\ValueObject\LogStatus;
use Paraunit\Logs\ValueObject\Test;
use PHPUnit\Event\Test\Finished;
use PHPUnit\Event\Test\FinishedSubscriber;

/**
 * @template-extends AbstractTestHook<Finished>
 */
class TestFinished extends AbstractTestHook implements FinishedSubscriber
{
    public function notify(Finished $event): void
    {
        $this->write(LogStatus::Finished, Test::fromPHPUnitTest($event->test()), null);
    }
}
