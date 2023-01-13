<?php

declare(strict_types=1);

namespace Paraunit\Parser\JSON;

use Paraunit\Parser\ValueObject\LogData;
use Paraunit\Parser\ValueObject\TestStatus;
use Paraunit\Process\AbstractParaunitProcess;
use Paraunit\TestResult\Interfaces\TestResultHandlerInterface;
use Paraunit\TestResult\Interfaces\TestResultInterface;
use Paraunit\TestResult\MuteTestResult;
use Paraunit\TestResult\TestResultWithMessage;

class GenericParser implements ParserChainElementInterface
{
    /**
     * @param TestStatus $status The status that the parser should catch
     */
    public function __construct(
        protected readonly TestResultHandlerInterface $testResultContainer,
        protected readonly TestStatus $status,
    ) {
    }

    /**
     * {@inheritdoc}
     */
    public function handleLogItem(AbstractParaunitProcess $process, LogData $logItem): ?TestResultInterface
    {
        if ($this->logMatches($logItem)) {
            $testResult = $this->createFromLog($logItem);
            $this->testResultContainer->handleTestResult($process, $testResult);
            $process->setWaitingForTestResult(false);

            return $testResult;
        }

        return null;
    }

    protected function logMatches(LogData $log): bool
    {
        return $log->status === $this->status;
    }

    private function createFromLog(LogData $logItem): TestResultInterface
    {
        if ($logItem->message) {
            return new TestResultWithMessage($logItem->test, $logItem->message);
        }

        return new MuteTestResult($logItem->test);
    }
}
