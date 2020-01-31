<?php

namespace Paraunit\Lifecycle;

use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\EventDispatcher\LegacyEventDispatcherProxy;

if (class_exists(Event::class)) {
    abstract class AbstractEvent extends Event
    {

    }
} else {
    abstract class AbstractEvent
    {

    }
}
