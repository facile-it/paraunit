<?php

declare(strict_types=1);

namespace Paraunit\Parser\JSON;

use Paraunit\Configuration\ChunkSize;
use Paraunit\Process\AbstractParaunitProcess;
use Paraunit\TestResult\Interfaces\TestResultHandlerInterface;
use Paraunit\TestResult\Interfaces\TestResultInterface;
use Paraunit\TestResult\NullTestResult;
use Paraunit\TestResult\TestResultWithAbnormalTermination;

class AbnormalTerminatedParser extends GenericParser
{
    /** @var ChunkSize */
    private $chunkSize;

    /** @var string */
    private $lastStartedTest = '[UNKNOWN]';

    public function __construct(
        TestResultHandlerInterface $testResultHandler,
        ChunkSize $chunkSize
    ) {
        parent::__construct($testResultHandler, LogFetcher::LOG_ENDING_STATUS);
        $this->chunkSize = $chunkSize;
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

        if ($this->chunkSize->isChunked()) {
            $suiteName = basename($process->getFilename());
        } else {
            $suiteName = explode('::', $logItem->getTest())[0];
        }
        $process->setTestClassName($suiteName);
    }
}
