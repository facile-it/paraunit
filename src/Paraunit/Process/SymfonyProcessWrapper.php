<?php
declare(strict_types=1);

namespace Paraunit\Process;

use Symfony\Component\Process\Process;
use Symfony\Component\Process\ProcessBuilder;

/**
 * Class SymfonyProcessWrapper
 * @package Paraunit\Process
 */
class SymfonyProcessWrapper extends AbstractParaunitProcess
{
    /** @var ProcessBuilder */
    private $processBuilder;

    /** @var Process */
    private $process;
    /** @var string */
    protected $commandLine;

    /**
     * {@inheritdoc}
     */
    public function __construct(ProcessBuilder $processBuilder, string $filename)
    {
        parent::__construct($filename);
        $this->processBuilder = $processBuilder;
    }

    public function isTerminated(): bool
    {
        return $this->process->isTerminated();
    }

    /**
     * @param array $env
     * @throws \Symfony\Component\Process\Exception\LogicException
     * @throws \Symfony\Component\Process\Exception\RuntimeException
     */
    public function start(array $env = [])
    {
        $this->processBuilder->addEnvironmentVariables($env);
        $this->process = $this->processBuilder->getProcess();
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
     */
    public function reset()
    {
        parent::reset();

        $this->process = null;
    }

    public function getCommandLine(): string
    {
        return $this->commandLine;
    }

    public function isRunning(): bool
    {
        return $this->process->isRunning();
    }

    public function wait()
    {
        $this->process->wait();
    }
}
