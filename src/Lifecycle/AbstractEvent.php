<?php

namespace Paraunit\Lifecycle;

use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\EventDispatcher\LegacyEventDispatcherProxy;

if (class_exists(Event::class)) {
    class_alias(Event::class, AbstractEvent::class);
} else {
    abstract class AbstractEvent
    {

    }
}
