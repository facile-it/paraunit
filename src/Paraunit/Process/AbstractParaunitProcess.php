<?php

declare(strict_types=1);

namespace Paraunit\Process;

use Paraunit\TestResult\Interfaces\PrintableTestResultInterface;
use Paraunit\TestResult\TestResultWithAbnormalTermination;

abstract class AbstractParaunitProcess
{
    /** @var int */
    protected $retryCount = 0;

    /** @var bool */
    protected $shouldBeRetried;

    /** @var string */
    protected $uniqueId;

    /** @var string */
    protected $filename;

    /** @var string */
    protected $testClassName;

    /** @var PrintableTestResultInterface[] */
    protected $testResults;

    /** @var bool */
    private $waitingForTestResult;

    /**
     * @throws \InvalidArgumentException
     */
    public function __construct(string $filename)
    {
        $this->filename = $filename;
        $this->uniqueId = md5($this->filename);
        $this->testResults = [];
        $this->waitingForTestResult = true;
        $this->shouldBeRetried = false;
    }

    abstract public function getOutput(): string;

    abstract public function isTerminated(): bool;

    abstract public function getCommandLine(): string;

    abstract public function getExitCode(): ?int;

    abstract public function start(int $pipelineNumber);

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
        $this->reset();
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
     * @return PrintableTestResultInterface[]
     */
    public function getTestResults(): array
    {
        return $this->testResults;
    }

    public function addTestResult(PrintableTestResultInterface $testResult): void
    {
        $this->testResults[] = $testResult;
        $this->waitingForTestResult = false;
    }

    public function hasAbnormalTermination(): bool
    {
        return end($this->testResults) instanceof TestResultWithAbnormalTermination;
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
