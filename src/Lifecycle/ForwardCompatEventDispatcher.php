<?php

declare(strict_types=1);

namespace Paraunit\Lifecycle;

use Psr\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface as SymfonyEventDispatcherInterface;

class ForwardCompatEventDispatcher implements EventDispatcherInterface
{
    /** @var SymfonyEventDispatcherInterface */
    private $eventDispatcher;

    public function __construct(SymfonyEventDispatcherInterface $eventDispatcher)
    {
        $this->eventDispatcher = $eventDispatcher;
    }

    public function dispatch(object $event): object
    {
        $this->eventDispatcher->dispatch(\get_class($event), $event);

        return $event;
    }
}
