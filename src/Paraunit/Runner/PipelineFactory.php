<?php

declare(strict_types=1);

namespace Paraunit\Runner;

use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class PipelineFactory
{
    /** @var EventDispatcherInterface */
    private $dispatcher;

    public function __construct(EventDispatcherInterface $dispatcher)
    {
        $this->dispatcher = $dispatcher;
    }

    public function create(int $pipelineNumber): Pipeline
    {
        return new Pipeline($this->dispatcher, $pipelineNumber);
    }
}
