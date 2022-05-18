<?php

declare(strict_types=1);

namespace Paraunit\Runner;

use Paraunit\Lifecycle\ProcessStarted;
use Paraunit\Lifecycle\ProcessTerminated;
use Paraunit\Process\AbstractParaunitProcess;
use Psr\EventDispatcher\EventDispatcherInterface;

class Pipeline
{
    /** @var EventDispatcherInterface */
    private $dispatcher;

    /** @var AbstractParaunitProcess|null */
    private $process;

    /** @var int */
    private $number;

    public function __construct(EventDispatcherInterface $dispatcher, int $number)
    {
        $this->dispatcher = $dispatcher;
        $this->number = $number;
    }

    public function execute(AbstractParaunitProcess $process): void
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
        if ($this->process) {
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

    /**
     * @return AbstractParaunitProcess|null
     */
    public function getProcess()
    {
        return $this->process;
    }

    private function handleProcessTermination(): void
    {
        if ($this->process) {
            $this->dispatcher->dispatch(new ProcessTerminated($this->process));
            $this->process = null;
        }
    }
}
