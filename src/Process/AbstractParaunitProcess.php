<?php

declare(strict_types=1);

namespace Paraunit\Process;

use Paraunit\Process\AbstractParaunitProcess\AbstractInfo;
use Paraunit\TestResult\Interfaces\PrintableTestResultInterface;

abstract class AbstractParaunitProcess extends AbstractInfo
{
    /** @var int */
    protected $retryCount = 0;

    protected bool $shouldBeRetried = false;

    protected string $uniqueId;

    /** @var string */
    protected $filename;

    /** @var string|null */
    protected $testClassName = null;

    /** @var PrintableTestResultInterface[] */
    protected array $testResults = [];

    private bool $waitingForTestResult = true;

    public function __construct(string $filename)
    {
        $this->filename = $filename;
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

    public function isWaitingForTestResult(): bool
    {
        return $this->waitingForTestResult;
    }

    public function setWaitingForTestResult(bool $waitingForTestResult): void
    {
        $this->waitingForTestResult = $waitingForTestResult;
    }
}
