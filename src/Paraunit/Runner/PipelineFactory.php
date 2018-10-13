<?php

declare(strict_types=1);

namespace Paraunit\Runner;

use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * Class PipelineFactory
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
     *
     * @return Pipeline
     */
    public function create(int $pipelineNumber): Pipeline
    {
        return new Pipeline($this->dispatcher, $pipelineNumber);
    }
}
