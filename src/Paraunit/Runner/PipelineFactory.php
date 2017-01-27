<?php

namespace Paraunit\Runner;

use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * Class PipelineFactory
 * @package Paraunit\Runner
 */
class PipelineFactory
{
    /** @var EventDispatcherInterface */
    private $dispatcher;

    public function __construct(EventDispatcherInterface $dispatcher)
    {
        $this->dispatcher = $dispatcher;
    }

    /**
     * @param int $pipelineNumber
     * @return Pipeline
     */
    public function create($pipelineNumber)
    {
        return new Pipeline($this->dispatcher, $pipelineNumber);
    }
}
