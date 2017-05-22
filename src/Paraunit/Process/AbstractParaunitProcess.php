<?php

namespace Paraunit\Process;

use Paraunit\TestResult\Interfaces\PrintableTestResultInterface;
use Paraunit\TestResult\TestResultWithAbnormalTermination;

/**
 * Class AbstractParaunitProcess
 * @package Paraunit\Process
 */
abstract class AbstractParaunitProcess implements ParaunitProcessInterface, RetryAwareInterface, ProcessWithResultsInterface
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
     * {@inheritdoc}
     */
    public function __construct(string $commandLine, string $uniqueId)
    {
        $this->uniqueId = $uniqueId;

        $filename = array();
        if (preg_match('/(?<=[\/\\\])[A-z]+\.php/', $commandLine, $filename) === 1) {
            $this->filename = $filename[0];
        }

        $this->shouldBeRetried = false;
        $this->testResults = array();
        $this->waitingForTestResult = true;
    }

    public function getUniqueId(): string
    {
        return $this->uniqueId;
    }

    public function getRetryCount(): int
    {
        return $this->retryCount;
    }

    public function increaseRetryCount()
    {
        ++$this->retryCount;
    }

    public function markAsToBeRetried()
    {
        $this->shouldBeRetried = true;
        $this->testResults = array();
    }

    public function isToBeRetried(): bool
    {
        return $this->shouldBeRetried;
    }

    public function reset()
    {
        $this->shouldBeRetried = false;
    }

    public function getFilename(): string
    {
        return $this->filename;
    }

    /**
     * @return string|null
     */
    public function getTestClassName()
    {
        return $this->testClassName;
    }

    public function setTestClassName(string $testClassName)
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

    public function addTestResult(PrintableTestResultInterface $testResult)
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

    public function setWaitingForTestResult(bool $waitingForTestResult)
    {
        $this->waitingForTestResult = (bool)$waitingForTestResult;
    }
}
