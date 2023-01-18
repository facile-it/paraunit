<?php

declare(strict_types=1);

namespace Paraunit\Logs\JSON;

use Paraunit\Logs\ValueObject\LogData;
use Paraunit\Logs\ValueObject\Test;
use Paraunit\Logs\ValueObject\TestStatus;
use Paraunit\Printer\ValueObject\TestOutcome;
use Paraunit\Process\AbstractParaunitProcess;
use Paraunit\TestResult\TestResult;
use Paraunit\TestResult\TestResultContainer;
use Paraunit\TestResult\TestResultWithMessage;
use Paraunit\TestResult\TestWithAbnormalTermination;

final class LogHandler
{
    private Test $currentTest;

    private ?TestResult $lastTestResult = null;

    private int $preparedTestCount = 0;

    public function __construct(private readonly TestResultContainer $testResultContainer)
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
        if ($log->status === TestStatus::Started) {
            $this->preparedTestCount += (int) $log->message;
            $this->currentTest = $log->test;

            return;
        }

        if (in_array($log->status, [TestStatus::LogTerminated, TestStatus::Prepared], true)) {
            $this->flushLastStatus($process, $log);

            return;
        }

        $result = TestResult::from($log);

        if ($result->isMoreImportantThan($this->lastTestResult)) {
            $this->lastTestResult = $result;
        }
    }

    private function flushLastStatus(AbstractParaunitProcess $process, LogData $log): void
    {
        if ($this->lastTestResult === null && $log->status === TestStatus::Prepared) {
            $this->currentTest = $log->test;

            return;
        }

        if ($log->status === TestStatus::LogTerminated && $this->preparedTestCount === 0) {
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
}
