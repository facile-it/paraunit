<?php

declare(strict_types=1);

namespace Tests\Stub;

use Paraunit\Process\AbstractParaunitProcess;

class StubbedParaunitProcess extends AbstractParaunitProcess
{
    /** @var string */
    private $output;

    /** @var string */
    private $commandLine;

    /** @var int */
    private $exitCode;

    /**
     * {@inheritdoc}
     */
    public function __construct(
        string $filename = 'testFilename',
        string $uniqueId = null
    ) {
        parent::__construct($filename);

        if (null !== $uniqueId) {
            $this->uniqueId = $uniqueId;
        }

        $this->commandLine = 'phpunit /stub/test';
        $this->exitCode = 0;
    }

    public function setFilename(string $filename)
    {
        $this->filename = $filename;
    }

    public function setIsToBeRetried(bool $isToBeRetried)
    {
        $this->shouldBeRetried = $isToBeRetried;
    }

    public function getCommandLine(): string
    {
        return $this->commandLine;
    }

    public function setOutput(string $output)
    {
        $this->output = $output;
    }

    public function setExitCode(int $exitCode)
    {
        $this->exitCode = $exitCode;
    }

    public function getOutput(): string
    {
        return $this->output ?? '';
    }

    public function isTerminated(): bool
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function start(int $pipeline)
    {
        $this->reset();
    }

    public function getExitCode(): int
    {
        return $this->exitCode;
    }
}
