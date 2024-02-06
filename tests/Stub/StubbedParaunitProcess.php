<?php

declare(strict_types=1);

namespace Tests\Stub;

use Paraunit\Process\Process;

class StubbedParaunitProcess implements Process
{
    public bool $shouldBeRetried = false;

    public ?string $output = null;

    public ?string $errorOutput = null;

    public int $exitCode = 0;

    public int $retryCount = 0;

    /**
     * @inheritDoc
     */
    public function __construct(
        public string $filename = 'testFilename',
        public string $uniqueId = ''
    ) {
        if ($this->uniqueId === '') {
            $this->uniqueId = md5($this->filename);
        }
    }

    public function setIsToBeRetried(bool $isToBeRetried): void
    {
        $this->shouldBeRetried = $isToBeRetried;
    }

    public function getCommandLine(): string
    {
        return 'phpunit /stub/test';
    }

    public function getOutput(): string
    {
        return $this->output ?? '';
    }

    public function getErrorOutput(): string
    {
        return $this->errorOutput ?? '';
    }

    public function isTerminated(): bool
    {
        return true;
    }

    /**
     * @inheritDoc
     */
    public function start(int $pipelineNumber): void
    {
        $this->shouldBeRetried = false;
    }

    public function getExitCode(): ?int
    {
        return $this->exitCode;
    }

    public function getUniqueId(): string
    {
        return $this->uniqueId;
    }

    public function isToBeRetried(): bool
    {
        return $this->shouldBeRetried;
    }

    public function markAsToBeRetried(): void
    {
        ++$this->retryCount;
        $this->shouldBeRetried = true;
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
