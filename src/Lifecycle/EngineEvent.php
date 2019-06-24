<?php

declare(strict_types=1);

namespace Paraunit\Lifecycle;

use Symfony\Component\EventDispatcher\Event;

class EngineEvent extends Event
{
    public const BEFORE_START = 'engine_event.before_start';

    public const START = 'engine_event.start';

    public const END = 'engine_event.end';
}
