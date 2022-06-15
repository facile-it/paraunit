<?php

declare(strict_types=1);

namespace Paraunit\Process;

use Paraunit\Configuration\EnvVariables;
use Symfony\Component\Process\Process;

class SymfonyProcessWrapper extends AbstractParaunitProcess
{
    /** @var Process */
    private $process;

    /**
     * {@inheritdoc}
     */
    public function __construct(Process $process, string $filename)
    {
        parent::__construct($filename);
        $this->process = $process;
    }

    public function isTerminated(): bool
    {
        return $this->process->isTerminated();
    }

    public function start(int $pipelineNumber): void
    {
        $this->reset();
        $env = $this->process->getEnv();
        $env[EnvVariables::PROCESS_UNIQUE_ID] = $this->getUniqueId();
        $env[EnvVariables::PIPELINE_NUMBER] = $pipelineNumber;

        $this->process->setEnv($env);
        $this->process->start();
    }

    /**
     * @throws \Symfony\Component\Process\Exception\LogicException
     */
    public function getOutput(): string
    {
        return $this->process->getOutput();
    }

    /**
     * @throws \Symfony\Component\Process\Exception\LogicException
     */
    public function getErrorOutput(): string
    {
        return $this->process->getErrorOutput();
    }

    /**
     * @throws \Symfony\Component\Process\Exception\RuntimeException
     */
    public function getExitCode(): ?int
    {
        return $this->process->getExitCode();
    }

    public function getCommandLine(): string
    {
        return $this->process->getCommandLine();
    }
}
