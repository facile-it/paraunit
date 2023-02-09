<?php

declare(strict_types=1);

namespace Paraunit\Process;

use Paraunit\TestResult\ValueObject\TestResult;

abstract class AbstractParaunitProcess implements Process
{
    /** @var int */
    protected $retryCount = 0;

    protected bool $shouldBeRetried = false;

    protected string $uniqueId;

    protected ?string $testClassName = null;

    /** @var TestResult[] */
    protected array $testResults = [];

    // TODO - remove?
    private bool $waitingForTestResult = true;

    public function __construct(protected string $filename)
    {
        $this->uniqueId = md5($this->filename);
    }

    abstract public function start(int $pipelineNumber): void;

    public function getUniqueId(): string
    {
        return $this->uniqueId;
    }

    public function getRetryCount(): int
    {
        return $this->retryCount;
    }

    public function increaseRetryCount(): void
    {
        ++$this->retryCount;
    }

    public function markAsToBeRetried(): void
    {
        $this->increaseRetryCount();
        $this->shouldBeRetried = true;
    }

    public function isToBeRetried(): bool
    {
        return $this->shouldBeRetried;
    }

    public function reset(): void
    {
        $this->shouldBeRetried = false;
        $this->testResults = [];
    }

    public function getFilename(): string
    {
        return $this->filename;
    }

    public function getTestClassName(): ?string
    {
        return $this->testClassName;
    }

    public function setTestClassName(string $testClassName): void
    {
        $this->testClassName = $testClassName;
    }

    /**
     * @return TestResult[]
     */
    public function getTestResults(): array
    {
        return $this->testResults;
    }

    public function addTestResult(TestResult $testResult): void
    {
        $this->testResults[] = $testResult;
        $this->waitingForTestResult = false;
    }

    public function isWaitingForTestResult(): bool
    {
        return $this->waitingForTestResult;
    }

    public function setWaitingForTestResult(bool $waitingForTestResult): void
    {
        $this->waitingForTestResult = $waitingForTestResult;
    }
}
