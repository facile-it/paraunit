<?php

declare(strict_types=1);

namespace Paraunit\Runner;

use Psr\EventDispatcher\EventDispatcherInterface;

class PipelineFactory
{
    public function __construct(private readonly EventDispatcherInterface $dispatcher) {}

    /**
     * @param positive-int $pipelineNumber
     */
    public function create(int $pipelineNumber): Pipeline
    {
        return new Pipeline($this->dispatcher, $pipelineNumber);
    }
}
