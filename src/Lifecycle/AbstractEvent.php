<?php

declare(strict_types=1);

namespace Paraunit\Lifecycle;

use Symfony\Component\EventDispatcher\Event;

if (class_exists(Event::class)) {
    class_alias(Event::class, AbstractEvent::class);
} else {
    abstract class AbstractEvent
    {
    }
}
