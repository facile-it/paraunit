<?php

namespace Paraunit\Runner;

use Paraunit\Process\ParaunitProcessInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * Class PipelineCollection
 * @package Paraunit\Runner
 */
class PipelineCollection
{
    /** @var Pipeline[] | \SplFixedArray */
    private $pipelines;

    public function __construct(PipelineFactory $pipelineFactory, $maxProcessNumber = 10)
    {
        $this->pipelines = new \SplFixedArray($maxProcessNumber);

        for ($pipelineNumber = 0; $pipelineNumber < $maxProcessNumber; $pipelineNumber++) {
            $this->pipelines->offsetSet($pipelineNumber, $pipelineFactory->create($pipelineNumber));
        }
    }

    /**
     * @param ParaunitProcessInterface $process
     * @return Pipeline
     */
    public function push(ParaunitProcessInterface $process)
    {
        do {
            foreach ($this->pipelines as $pipeline) {
                if ($pipeline->isFree()) {
                    $pipeline->execute($process);

                    return $pipeline;
                }
            }

            sleep(500);
        } while (1);
    }

    public function waitForCompletion()
    {
        foreach ($this->pipelines as $pipeline) {
            $pipeline->waitCompletion();
        }
    }
}
