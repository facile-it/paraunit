<?php

declare(strict_types=1);

namespace Paraunit\Runner;

use Paraunit\Process\AbstractParaunitProcess;

/**
 * Class PipelineCollection
 */
class PipelineCollection
{
    /** @var Pipeline[] | \SplFixedArray */
    private $pipelines;

    public function __construct(PipelineFactory $pipelineFactory, int $maxProcessNumber = 10)
    {
        $this->pipelines = new \SplFixedArray($maxProcessNumber);

        for ($pipelineNumber = 1; $pipelineNumber <= $maxProcessNumber; ++$pipelineNumber) {
            $this->pipelines->offsetSet($pipelineNumber - 1, $pipelineFactory->create($pipelineNumber));
        }
    }

    /**
     * @param AbstractParaunitProcess $process
     *
     * @throws \RuntimeException
     *
     * @return Pipeline
     */
    public function push(AbstractParaunitProcess $process): Pipeline
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

    public function isEmpty(): bool
    {
        foreach ($this->pipelines as $pipeline) {
            if (! $pipeline->isFree()) {
                return false;
            }
        }

        return true;
    }

    public function triggerProcessTermination()
    {
        foreach ($this->pipelines as $pipeline) {
            $pipeline->triggerTermination();
        }
    }
}
