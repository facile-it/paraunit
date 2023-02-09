<?php

declare(strict_types=1);

namespace Paraunit\Process;

use Paraunit\Configuration\EnvVariables;
use Symfony\Component\Process\Exception\LogicException;
use Symfony\Component\Process\Exception\RuntimeException;
use Symfony\Component\Process\Process as SymfonyProcess;

class SymfonyProcessWrapper implements Process
{
    private bool $shouldBeRetried = false;

    protected string $uniqueId;

    private int $retryCount = 0;

    public function __construct(
        private readonly SymfonyProcess $process,
        private readonly string $filename,
    ) {
        $this->uniqueId = md5($this->filename);
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
        $env[EnvVariables::PIPELINE_NUMBER] = (string) $pipelineNumber;

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

    public function markAsToBeRetried(): void
    {
        ++$this->retryCount;
        $this->shouldBeRetried = true;
    }

    public function getUniqueId(): string
    {
        return $this->uniqueId;
    }

    public function isToBeRetried(): bool
    {
        return $this->shouldBeRetried;
    }

    private function reset(): void
    {
        $this->shouldBeRetried = false;
    }

    public function getFilename(): string
    {
        return $this->filename;
    }

    public function getRetryCount(): int
    {
        return $this->retryCount;
    }
}
