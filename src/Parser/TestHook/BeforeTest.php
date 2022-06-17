<?php

declare(strict_types=1);

namespace Paraunit\Parser\TestHook;

use Paraunit\Parser\DTO\TestStatus;
use PHPUnit\Event\Test\Prepared;
use PHPUnit\Event\Test\PreparedSubscriber;

class BeforeTest extends AbstractTestHook implements PreparedSubscriber
{
    public function notify(Prepared $event): void
    {
        $this->write(TestStatus::Prepared, $event->test(), null);
    }
}
