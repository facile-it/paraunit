<?php

declare(strict_types=1);

namespace Paraunit\Parser\JSON;

use Paraunit\Configuration\ChunkSize;
use Paraunit\Parser\ValueObject\LogData;
use Paraunit\Parser\ValueObject\Test;
use Paraunit\Parser\ValueObject\TestStatus;
use Paraunit\Process\AbstractParaunitProcess;
use Paraunit\TestResult\Interfaces\TestResultHandlerInterface;
use Paraunit\TestResult\Interfaces\TestResultInterface;
use Paraunit\TestResult\NullTestResult;
use Paraunit\TestResult\TestResultWithAbnormalTermination;

class AbnormalTerminatedParser extends GenericParser
{
    private Test $lastStartedTest;

    public function __construct(
        TestResultHandlerInterface $testResultHandler,
        private readonly ChunkSize $chunkSize,
    ) {
        parent::__construct($testResultHandler, TestStatus::LogTerminated);
        $this->lastStartedTest = Test::unknown();
    }

    public function handleLogItem(AbstractParaunitProcess $process, LogData $logItem): ?TestResultInterface
    {
        if ($logItem->status === TestStatus::Prepared) {
            $process->setWaitingForTestResult(true);
            $this->saveTestFQCN($process, $logItem);

            return new NullTestResult();
        }

        if (! $process->isWaitingForTestResult() && $this->logMatches($logItem)) {
            return new NullTestResult();
        }

        if ($this->logMatches($logItem)) {
            $testResult = new TestResultWithAbnormalTermination($this->lastStartedTest);
            $this->testResultContainer->handleTestResult($process, $testResult);
            $process->setWaitingForTestResult(false);

            return $testResult;
        }

        return null;
    }

    private function saveTestFQCN(AbstractParaunitProcess $process, LogData $logItem): void
    {
        $this->lastStartedTest = $logItem->test;

        if ($process->getTestClassName()) {
            return;
        }

        $suiteName = $this->chunkSize->isChunked()
            ? basename($process->getFilename())
            : explode('::', $logItem->test->name)[0];

        $process->setTestClassName($suiteName);
    }
}
