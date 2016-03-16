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
    protected $process;

    /**
     * {@inheritdoc}
     */
    public function __construct($commandLine, $uniqueId)
    {
        parent::__construct($commandLine, $uniqueId);
        $this->process = new Process($commandLine);
    }

    /**
     * @return bool
     */
    public function isTerminated()
    {
        return $this->process->isTerminated();
    }

    /**
     *
     */
    public function start()
    {
        $this->process->start();
    }

    /**
     * {@inheritdoc}
     */
    public function getOutput()
    {
        return $this->process->getOutput();
    }

    /**
     * {@inheritdoc}
     */
    public function getExitCode()
    {
        return $this->process->getExitCode();
    }

    /**
     * {@inheritdoc}
     */
    public function restart()
    {
        $this->reset()->start();

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function reset()
    {
        // RESET DELLO STATO
        parent::reset();

        $this->process = new Process($this->process->getCommandLine());

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getCommandLine()
    {
        return $this->process->getCommandLine();
    }

    /**
     * {@inheritdoc}
     */
    public function isRunning()
    {
        return $this->process->isRunning();
    }
}
