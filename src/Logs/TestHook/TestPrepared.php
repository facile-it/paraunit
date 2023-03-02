<?php

declare(strict_types=1);

namespace Paraunit\Logs\TestHook;

use Paraunit\Logs\ValueObject\LogStatus;
use Paraunit\Logs\ValueObject\Test;
use PHPUnit\Event\Test\Prepared;
use PHPUnit\Event\Test\PreparedSubscriber;

/**
 * @template-extends AbstractTestHook<Prepared>
 */
class TestPrepared extends AbstractTestHook implements PreparedSubscriber
{
    public function notify(Prepared $event): void
    {
        $this->write(LogStatus::Prepared, Test::fromPHPUnitTest($event->test()), null);
    }
}
