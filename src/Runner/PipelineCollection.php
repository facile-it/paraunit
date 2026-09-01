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
        return array_any($this->pipelines, static fn(Pipeline $pipeline): bool => $pipeline->isFree());
    }

    public function isEmpty(): bool
    {
        return array_all($this->pipelines, static fn(Pipeline $pipeline): bool => $pipeline->isFree());
    }

    /**
     * @return array<int, Process>
     */
    public function getRunningProcesses(): array
    {
        $processes = [];
        foreach ($this->pipelines as $pipeline) {
            $process = $pipeline->getProcess();
            if ($process instanceof Process) {
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
