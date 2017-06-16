<?php

namespace Paraunit\Runner;

use Paraunit\Process\ParaunitProcessInterface;

/**
 * Class PipelineCollection
 * @package Paraunit\Runner
 */
class PipelineCollection
{
    /** @var Pipeline[] | \SplFixedArray */
    private $pipelines;

    public function __construct(PipelineFactory $pipelineFactory, int $maxProcessNumber = 10)
    {
        $this->pipelines = new \SplFixedArray($maxProcessNumber);

        for ($pipelineNumber = 0; $pipelineNumber < $maxProcessNumber; $pipelineNumber++) {
            $this->pipelines->offsetSet($pipelineNumber, $pipelineFactory->create($pipelineNumber));
        }
    }

    /**
     * @param ParaunitProcessInterface $process
     * @return Pipeline
     * @throws \RuntimeException
     */
    public function push(ParaunitProcessInterface $process): Pipeline
    {
        foreach ($this->pipelines as $pipeline) {
            if ($pipeline->isFree()) {
                $pipeline->execute($process);

                return $pipeline;
            }
        }

        throw new \RuntimeException('Cannot find an available pipeline');
    }

    public function hasEmptySlots(): bool
    {
        foreach ($this->pipelines as $pipeline) {
            if ($pipeline->isFree()) {
                return true;
            }
        }

        return false;
    }

    public function checkRunningState(): bool
    {
        $isRunning = false;

        foreach ($this->pipelines as $pipeline) {
            if (! $pipeline->isTerminated() || ! $pipeline->isFree()) {
                $isRunning = true;
            }
        }

        return $isRunning;
    }
}
