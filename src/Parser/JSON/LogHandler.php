<?php

namespace Paraunit\Parser\JSON;

use Paraunit\Parser\ValueObject\LogData;
use Paraunit\Parser\ValueObject\Test;
use Paraunit\Parser\ValueObject\TestStatus;
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
        $result = match ($log->status) {
            TestStatus::LogTerminated,
            TestStatus::Prepared => $this->flushLastStatus($process, $log),
            TestStatus::Errored,
            TestStatus::Failed,
            TestStatus::MarkedIncomplete,
            TestStatus::Skipped,
            TestStatus::Passed,
            TestStatus::Unknown,
            TestStatus::WarningTriggered,
            TestStatus::ConsideredRisky,
            TestStatus::Finished => TestResult::from($log),
        };
        
        if ($result instanceof TestResultWithMessage) {
            $this->testResultContainer->handleTestResult($process, $result);
        }

        if ($result?->isMoreImportantThan($this->lastTestResult)) {
            $this->lastTestResult = $result;
        }
    }

    /**
     * @return null
     */
    private function flushLastStatus(AbstractParaunitProcess $process, LogData $log): ?TestResult
    {
        if ($this->lastTestResult === null) {
            $this->lastTestResult = new TestWithAbnormalTermination($this->currentTest, $process);
        }

        $process->addTestResult($this->lastTestResult);
        $this->lastTestResult = null;
        $this->currentTest = $log->test;

        return null;
    }
}
