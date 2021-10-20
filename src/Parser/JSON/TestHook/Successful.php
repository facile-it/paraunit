<?php

declare(strict_types=1);

namespace Paraunit\Parser\JSON\TestHook;

use Paraunit\Parser\JSON\Log;
use PHPUnit\Event\Test\Passed;
use PHPUnit\Event\Test\PassedSubscriber;

class Successful extends AbstractTestHook implements PassedSubscriber
{
    public function notify(Passed $event): void
    {
        $this->write(Log::STATUS_SUCCESSFUL, $event->test()->name(), null);
    }
}
