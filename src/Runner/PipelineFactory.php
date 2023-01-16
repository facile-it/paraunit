<?php

declare(strict_types=1);

namespace Paraunit\Runner;

use Psr\EventDispatcher\EventDispatcherInterface;

class PipelineFactory
{
    public function __construct(private readonly EventDispatcherInterface $dispatcher)
    {
    }

    public function create(int $pipelineNumber): Pipeline
    {
        return new Pipeline($this->dispatcher, $pipelineNumber);
    }
}
