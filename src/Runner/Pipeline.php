<?php

declare(strict_types=1);

namespace Paraunit\Runner;

use Paraunit\Lifecycle\ProcessStarted;
use Paraunit\Lifecycle\ProcessTerminated;
use Paraunit\Process\Process;
use Psr\EventDispatcher\EventDispatcherInterface;

class Pipeline
{
    private ?Process $process = null;

    public function __construct(
        private readonly EventDispatcherInterface $dispatcher,
        /** @var positive-int $number */
        private readonly int $number
    ) {
    }

    public function execute(Process $process): void
    {
        if (! $this->isFree()) {
            throw new \RuntimeException('This pipeline is not free');
        }

        $this->process = $process;
        $this->process->start($this->number);

        $this->dispatcher->dispatch(new ProcessStarted($process));
    }

    public function isFree(): bool
    {
        return $this->process === null;
    }

    public function isTerminated(): bool
    {
        if ($this->process !== null) {
            return $this->process->isTerminated();
        }

        return true;
    }

    public function triggerTermination(): bool
    {
        if (null === $this->process) {
            return false;
        }

        if ($this->process->isTerminated()) {
            $this->handleProcessTermination();

            return true;
        }

        return false;
    }

    public function getNumber(): int
    {
        return $this->number;
    }

    public function getProcess(): ?Process
    {
        return $this->process;
    }

    private function handleProcessTermination(): void
    {
        if ($this->process !== null) {
            $this->dispatcher->dispatch(new ProcessTerminated($this->process));
            $this->process = null;
        }
    }
}
