<?php

declare(strict_types=1);

namespace Paraunit\Parser\TestHook;

use Paraunit\Parser\JSON\Log;
use Paraunit\Parser\TestStatus;
use PHPUnit\Event\Test\Passed;
use PHPUnit\Event\Test\PassedSubscriber;

class Successful extends AbstractTestHook implements PassedSubscriber
{
    public function notify(Passed $event): void
    {
        $this->write(TestStatus::Passed, $event->test(), null);
    }
}
