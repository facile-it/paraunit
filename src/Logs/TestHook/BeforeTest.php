<?php

declare(strict_types=1);

namespace Paraunit\Logs\TestHook;

use Paraunit\Logs\ValueObject\Test;
use Paraunit\Logs\ValueObject\TestStatus;
use PHPUnit\Event\Test\Prepared;
use PHPUnit\Event\Test\PreparedSubscriber;

class BeforeTest extends AbstractTestHook implements PreparedSubscriber
{
    public function notify(Prepared $event): void
    {
        $this->write(TestStatus::Prepared, Test::fromPHPUnitTest($event->test()), null);
    }
}
