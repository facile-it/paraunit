<?php

declare(strict_types=1);

namespace Paraunit\Logs\JSON;

use Paraunit\Lifecycle\TestCompleted;
use Paraunit\Logs\ValueObject\LogData;
use Paraunit\Logs\ValueObject\LogStatus;
use Paraunit\Logs\ValueObject\Test;
use Paraunit\Process\Process;
use Paraunit\TestResult\TestResultContainer;
use Paraunit\TestResult\TestWithAbnormalTermination;
use Paraunit\TestResult\ValueObject\TestIssue;
use Paraunit\TestResult\ValueObject\TestOutcome;
use Paraunit\TestResult\ValueObject\TestResult;
use Psr\EventDispatcher\EventDispatcherInterface;

class LogHandler
{
    private Test $currentTest;

    private TestOutcome|TestIssue|null $currentTestOutcome = null;

    private int $startedTestCount = 0;

    private int $preparedTestCount = 0;

    private int $finishedTestCount = 0;

    public function __construct(
        private readonly EventDispatcherInterface $eventDispatcher,
        private readonly TestResultContainer $testResultContainer,
    ) {
        $this->currentTest = Test::unknown();
        $this->reset();
    }

    public function reset(): void
    {
        $this->currentTest = Test::unknown();
        $this->currentTestOutcome = null;
        $this->startedTestCount = 0;
        $this->preparedTestCount = 0;
        $this->finishedTestCount = 0;
    }

    public function processLog(Process $process, LogData $log): void
    {
        if ($log->isIgnoredByTest()) {
            return;
        }

        if ($log->status === LogStatus::Started) {
            $this->currentTest = $log->test;
            $this->startedTestCount += (int) $log->message;

            return;
        }

        if ($log->status === LogStatus::Prepared) {
            // TODO - handle warnings happening between tests
            ++$this->preparedTestCount;
            $this->currentTest = $log->test;

            return;
        }

        if ($log->status === LogStatus::Finished) {
            ++$this->finishedTestCount;
            if ($this->currentTestOutcome === null) {
                throw new \LogicException('No outcome received');
            }

            $this->dispatchOutcome($this->currentTestOutcome);
            $this->currentTestOutcome = null;

            return;
        }

        if ($log->status === LogStatus::LogTerminated) {
            $this->handleLogEnding($process);

            return;
        }

        if ($log->isIgnoredByBaseline()) {
            $this->testResultContainer->addIgnoredByBaseline($log->status->toTestStatus());

            return;
        }

        $this->testResultContainer->addTestResult(TestResult::from($log));

        $testStatus = $log->status->toTestStatus();
        if ($testStatus->isMoreImportantThan($this->currentTestOutcome)) {
            $this->currentTestOutcome = $testStatus;
        }
    }

    private function handleLogEnding(Process $process): void
    {
        if ($process->getExitCode() === 0 && $this->preparedTestCount === 0) {
            $this->testResultContainer->addTestResult(new TestResult($this->currentTest, TestOutcome::NoTestExecuted));
        }

        if ($this->currentTestOutcome !== null) {
            $this->testResultContainer->addTestResult(new TestResult($this->currentTest, $this->currentTestOutcome));
            if ($this->currentTestOutcome instanceof TestOutcome) {
                $this->dispatchOutcome($this->currentTestOutcome);
            }
        }

        if ($this->preparedTestCount > $this->finishedTestCount) {
            // TODO - expose the number of missing tests?
            $this->testResultContainer->addTestResult(new TestWithAbnormalTermination($this->currentTest, $process));
            $this->dispatchOutcome(TestOutcome::AbnormalTermination);
        }
    }

    public function processNoLogAvailable(Process $process): void
    {
        $testResult = new TestWithAbnormalTermination(new Test($process->getFilename()), $process);

        $this->testResultContainer->addTestResult($testResult);
        $this->dispatchOutcome(TestOutcome::AbnormalTermination);
    }

    private function dispatchOutcome(TestOutcome|TestIssue $outcome): void
    {
        $this->eventDispatcher->dispatch(new TestCompleted($this->currentTest, $outcome));
    }
}
