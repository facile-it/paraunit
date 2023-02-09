<?php

declare(strict_types=1);

namespace Paraunit\Runner;

use Paraunit\Process\Process;

class PipelineCollection
{
    /** @var Pipeline[] */
    private array $pipelines = [];

    public function __construct(PipelineFactory $pipelineFactory, int $maxProcessNumber = 10)
    {
        for ($pipelineNumber = 1; $pipelineNumber <= $maxProcessNumber; ++$pipelineNumber) {
            $this->pipelines[] = $pipelineFactory->create($pipelineNumber);
        }
    }

    /**
     * @throws \RuntimeException
     */
    public function push(Process $process): Pipeline
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

    /**
     * @return array<int, Process>
     */
    public function getRunningProcesses(): array
    {
        $processes = [];
        foreach ($this->pipelines as $pipeline) {
            $process = $pipeline->getProcess();
            if ($process !== null) {
                $processes[] = $process;
            }
        }

        return $processes;
    }

    public function triggerProcessTermination(): void
    {
        foreach ($this->pipelines as $pipeline) {
            $pipeline->triggerTermination();
        }
    }
}
