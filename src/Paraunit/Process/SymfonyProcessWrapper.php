<?php

declare(strict_types=1);

namespace Paraunit\Process;

use Paraunit\Configuration\EnvVariables;
use Symfony\Component\Process\Process;

/**
 * Class SymfonyProcessWrapper
 */
class SymfonyProcessWrapper extends AbstractParaunitProcess
{
    /** @var Process */
    private $process;

    /** @var string */
    protected $commandLine;

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

    /**
     * @param int $pipelineNumber
     */
    public function start(int $pipelineNumber)
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
     *
     * @return string
     */
    public function getOutput(): string
    {
        return $this->process->getOutput() ?? '';
    }

    /**
     * @throws \Symfony\Component\Process\Exception\RuntimeException
     *
     * @return int|null
     */
    public function getExitCode()
    {
        return $this->process->getExitCode();
    }

    /**
     * @return string
     */
    public function getCommandLine(): string
    {
        return $this->process->getCommandLine();
    }
}
