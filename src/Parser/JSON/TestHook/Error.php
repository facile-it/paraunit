<?php

declare(strict_types=1);

namespace Paraunit\Parser\JSON\TestHook;

use Paraunit\Parser\JSON\Log;
use PHPUnit\Event\Test\Errored;
use PHPUnit\Event\Test\ErroredSubscriber;

class Error extends AbstractTestHook implements ErroredSubscriber
{
    public function notify(Errored $event): void
    {
        $this->write(Log::STATUS_ERROR, $event->test()->name(), $event->throwable()->message());
    }
}
