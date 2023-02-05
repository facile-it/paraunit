<?php

declare(strict_types=1);

namespace Paraunit\Logs\JSON;

use Paraunit\Logs\ValueObject\LogData;
use Paraunit\Logs\ValueObject\LogStatus;
use Paraunit\Logs\ValueObject\Test;
use Paraunit\Printer\ProgressPrinter;
use Paraunit\Process\AbstractParaunitProcess;
use Paraunit\TestResult\TestIssueContainer;
use Paraunit\TestResult\TestWithAbnormalTermination;
use Paraunit\TestResult\ValueObject\TestOutcome;
use Paraunit\TestResult\ValueObject\TestResult;

final class LogHandler
{
    private Test $currentTest;

    private ?TestOutcome $currentTestOutcome = null;

    private int $preparedTestCount = 0;

    private int $actuallyPreparedTestCount = 0;

    public function __construct(
        private readonly ProgressPrinter $progressPrinter,
        private readonly TestIssueContainer $testIssueContainer,
    ) {
        $this->reset();
    }

    public function reset(): void
    {
        $this->currentTest = Test::unknown();
        $this->currentTestOutcome = null;
        $this->preparedTestCount = 0;
        $this->actuallyPreparedTestCount = 0;
    }

    public function processLog(AbstractParaunitProcess $process, LogData $log): void
    {
        if ($log->status === LogStatus::Started) {
            $this->preparedTestCount += (int) $log->message;

            return;
        }

        if ($log->status === LogStatus::Prepared) {
            ++$this->actuallyPreparedTestCount;
            $this->currentTest = $log->test;
            $this->currentTestOutcome = null;

            return;
        }

        if ($log->status === LogStatus::LogTerminated) {
            if ($process->getExitCode() === 0) {
                return;
            }

            if ($this->currentTestOutcome !== null) {
                return;
            }

            $this->progressPrinter->printOutcome(TestOutcome::AbnormalTermination);
            // TODO - expose the number of unprepared tests?
            $this->testIssueContainer->addTestResult(new TestWithAbnormalTermination($this->currentTest, $process));

            return;
        }

        $testStatus = $log->status->toTestStatus();

        if ($testStatus instanceof TestOutcome) {
            $this->progressPrinter->printOutcome($testStatus);
            $this->currentTestOutcome = $testStatus;
        }

        $this->testIssueContainer->addTestResult(TestResult::from($log));
    }

    public function processNoLogAvailable(AbstractParaunitProcess $process): void
    {
        $testResult = new TestWithAbnormalTermination(new Test($process->getFilename()), $process);

        $process->addTestResult($testResult);
        $this->testIssueContainer->addTestResult($testResult);
    }
}
