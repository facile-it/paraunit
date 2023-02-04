<?php

declare(strict_types=1);

namespace Paraunit\Logs\JSON;

use Paraunit\Logs\ValueObject\LogData;
use Paraunit\Logs\ValueObject\LogStatus;
use Paraunit\Logs\ValueObject\Test;
use Paraunit\Process\AbstractParaunitProcess;
use Paraunit\TestResult\TestOutcomeContainer;
use Paraunit\TestResult\TestResultWithMessage;
use Paraunit\TestResult\TestWithAbnormalTermination;
use Paraunit\TestResult\ValueObject\TestOutcome;
use Paraunit\TestResult\ValueObject\TestResult;

final class LogHandler
{
    private Test $currentTest;

    private ?TestResult $lastTestResult = null;

    private int $preparedTestCount = 0;

    public function __construct(private readonly TestOutcomeContainer $testResultContainer)
    {
        $this->reset();
    }

    public function reset(): void
    {
        $this->currentTest = Test::unknown();
        $this->lastTestResult = null;
        $this->preparedTestCount = 0;
    }

    public function processLog(AbstractParaunitProcess $process, LogData $log): void
    {
        if ($log->status === LogStatus::Started) {
            $this->preparedTestCount += (int) $log->message;
            $this->currentTest = $log->test;

            return;
        }

        if (in_array($log->status, [LogStatus::LogTerminated, LogStatus::Prepared], true)) {
            $this->flushLastStatus($process, $log);

            return;
        }

        $result = TestResult::from($log);

        if ($this->lastTestResult === TestOutcome::Passed) {
            $this->lastTestResult = $result;
        }
    }

    private function flushLastStatus(AbstractParaunitProcess $process, LogData $log): void
    {
        if ($this->lastTestResult === null && $log->status === LogStatus::Prepared) {
            $this->currentTest = $log->test;

            return;
        }

        if ($log->status === LogStatus::LogTerminated && $this->preparedTestCount === 0) {
            $this->testResultContainer->addTestResult(new TestResult($this->currentTest, TestOutcome::NoTestExecuted));

            return;
        }

        if ($this->lastTestResult === null) {
            $this->lastTestResult = new TestWithAbnormalTermination($this->currentTest, $process);
        }

        $process->addTestResult($this->lastTestResult);
        if ($this->lastTestResult instanceof TestResultWithMessage) {
            $this->testResultContainer->addTestResult($this->lastTestResult);
        }

        $this->currentTest = $log->test;
        $this->lastTestResult = null;
    }

    public function processNoLogAvailable(AbstractParaunitProcess $process): void
    {
        $testResult = new TestWithAbnormalTermination(new Test($process->getFilename()), $process);

        $process->addTestResult($testResult);
        $this->testResultContainer->addTestResult($testResult);
    }
}
