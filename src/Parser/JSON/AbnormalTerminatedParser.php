<?php

declare(strict_types=1);

namespace Paraunit\Parser\JSON;

use Paraunit\Process\AbstractParaunitProcess;
use Paraunit\TestResult\Interfaces\TestResultHandlerInterface;
use Paraunit\TestResult\Interfaces\TestResultInterface;
use Paraunit\TestResult\NullTestResult;
use Paraunit\TestResult\TestResultWithAbnormalTermination;

class AbnormalTerminatedParser extends GenericParser
{
    /** @var string */
    private $lastStartedTest = '[UNKNOWN]';

    public function __construct(TestResultHandlerInterface $testResultHandler)
    {
        parent::__construct($testResultHandler, LogFetcher::LOG_ENDING_STATUS);
    }

    public function handleLogItem(AbstractParaunitProcess $process, Log $logItem): ?TestResultInterface
    {
        if ($logItem->getStatus() === Log::STATUS_TEST_START) {
            $process->setWaitingForTestResult(true);
            $this->saveTestFQCN($process, $logItem);

            return new NullTestResult();
        }

        if (! $process->isWaitingForTestResult() && $this->logMatches($logItem)) {
            return new NullTestResult();
        }

        if ($this->logMatches($logItem)) {
            $testResult = new TestResultWithAbnormalTermination($this->lastStartedTest ?? $logItem->getTest());
            $this->testResultContainer->handleTestResult($process, $testResult);
            $process->setWaitingForTestResult(false);

            return $testResult;
        }

        return null;
    }

    private function saveTestFQCN(AbstractParaunitProcess $process, Log $logItem): void
    {
        $this->lastStartedTest = $logItem->getTest();

        if ($process->getTestClassName()) {
            return;
        }

        $suiteName = explode('::', $logItem->getTest());
        $process->setTestClassName($suiteName[0]);
    }
}
