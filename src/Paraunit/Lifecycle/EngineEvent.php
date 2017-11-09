<?php

declare(strict_types=1);

namespace Paraunit\Lifecycle;

use Symfony\Component\EventDispatcher\Event;

/***
 * Class EngineEvent
 * @package Paraunit\Lifecycle
 */
class EngineEvent extends Event
{
    const BEFORE_START = 'engine_event.before_start';
    const START = 'engine_event.start';
    const END = 'engine_event.end';
}
