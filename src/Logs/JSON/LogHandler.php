<?php

declare(strict_types=1);

namespace Paraunit\Logs\JSON;

use Paraunit\Logs\ValueObject\LogData;
use Paraunit\Logs\ValueObject\Test;
use Paraunit\Logs\ValueObject\TestStatus;
use Paraunit\Process\AbstractParaunitProcess;
use Paraunit\TestResult\TestResult;
use Paraunit\TestResult\TestResultContainer;
use Paraunit\TestResult\TestResultWithMessage;
use Paraunit\TestResult\TestWithAbnormalTermination;

class LogHandler
{
    private Test $currentTest;

    private ?TestResult $lastTestResult = null;

    public function __construct(private readonly TestResultContainer $testResultContainer)
    {
        $this->currentTest = Test::unknown();
    }

    public function processLog(AbstractParaunitProcess $process, LogData $log): void
    {
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
