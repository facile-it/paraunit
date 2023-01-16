<?php

declare(strict_types=1);

namespace Paraunit\Process;

use Paraunit\Configuration\EnvVariables;
use Symfony\Component\Process\Exception\LogicException;
use Symfony\Component\Process\Exception\RuntimeException;
use Symfony\Component\Process\Process;

class SymfonyProcessWrapper extends AbstractParaunitProcess
{
    /**
     * {@inheritdoc}
     */
    public function __construct(private readonly Process $process, string $filename)
    {
        parent::__construct($filename);
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
     * @throws LogicException
     */
    public function getOutput(): string
    {
        return $this->process->getOutput();
    }

    /**
     * @throws LogicException
     */
    public function getErrorOutput(): string
    {
        return $this->process->getErrorOutput();
    }

    /**
     * @throws RuntimeException
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
