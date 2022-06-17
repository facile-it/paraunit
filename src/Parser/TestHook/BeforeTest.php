<?php

declare(strict_types=1);

namespace Paraunit\Parser\TestHook;

use Paraunit\Parser\JSON\Log;
use PHPUnit\Event\Test\Prepared;
use PHPUnit\Event\Test\PreparedSubscriber;

class BeforeTest extends AbstractTestHook implements PreparedSubscriber
{
    public function notify(Prepared $event): void
    {
        $this->write(Log::STATUS_TEST_START, $event->test(), null);
    }
}
