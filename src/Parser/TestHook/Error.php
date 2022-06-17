<?php

declare(strict_types=1);

namespace Paraunit\Parser\TestHook;

use Paraunit\Parser\DTO\TestStatus;
use PHPUnit\Event\Test\Errored;
use PHPUnit\Event\Test\ErroredSubscriber;

class Error extends AbstractTestHook implements ErroredSubscriber
{
    public function notify(Errored $event): void
    {
        $this->write(TestStatus::Errored, $event->test(), $event->throwable()->message());
    }
}
