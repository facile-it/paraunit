<?php

namespace Paraunit\Lifecycle;

use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\EventDispatcher\LegacyEventDispatcherProxy;

if (class_exists(LegacyEventDispatcherProxy::class)) {
    abstract class AbstractEvent
    {

    }
} else {
    abstract class AbstractEvent extends Event
    {

    }
}
