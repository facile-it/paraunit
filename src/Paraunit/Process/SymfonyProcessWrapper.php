<?php

namespace Paraunit\Process;

use Symfony\Component\Process\Process;

/**
 * Class SymfonyProcessWrapper
 * @package Paraunit\Process
 */
class SymfonyProcessWrapper extends AbstractParaunitProcess
{
    /** @var Process */
    private $process;

    /**
     * SymfonyProcessWrapper constructor.
     * @param string $commandLine
     * @param string $uniqueId
     * @throws \Symfony\Component\Process\Exception\RuntimeException
     */
    public function __construct(string $commandLine, string $uniqueId)
    {
        parent::__construct($commandLine, $uniqueId);
        $this->process = new Process($commandLine);
    }

    public function isTerminated(): bool
    {
        return $this->process->isTerminated();
    }

    /**
     * @return void
     * @throws \Symfony\Component\Process\Exception\LogicException
     * @throws \Symfony\Component\Process\Exception\RuntimeException
     */
    public function start()
    {
        $this->process->start();
    }

    /**
     * @return string
     * @throws \Symfony\Component\Process\Exception\LogicException
     */
    public function getOutput(): string
    {
        return $this->process->getOutput();
    }

    /**
     * @return int|null
     * @throws \Symfony\Component\Process\Exception\RuntimeException
     */
    public function getExitCode()
    {
        return $this->process->getExitCode();
    }

    /**
     * @return void
     * @throws \Symfony\Component\Process\Exception\RuntimeException
     * @throws \Symfony\Component\Process\Exception\LogicException
     */
    public function restart()
    {
        $this->reset();
        $this->start();
    }

    /**
     * @return void
     */
    public function reset()
    {
        parent::reset();

        $this->process = new Process($this->process->getCommandLine());
    }

    public function getCommandLine(): string
    {
        return $this->process->getCommandLine();
    }

    public function isRunning(): bool
    {
        return $this->process->isRunning();
    }
}
