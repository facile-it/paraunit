<?php

declare(strict_types=1);

namespace Paraunit\Parser\JSON;

use Paraunit\Process\AbstractParaunitProcess;
use Paraunit\TestResult\Interfaces\TestResultHandlerInterface;
use Paraunit\TestResult\Interfaces\TestResultInterface;
use Paraunit\TestResult\MuteTestResult;
use Paraunit\TestResult\TestResultWithMessage;

class GenericParser implements ParserChainElementInterface
{
    /** @var TestResultHandlerInterface */
    protected $testResultContainer;

    /** @var string */
    protected $status;

    /**
     * @param string $status The status that the parser should catch
     */
    public function __construct(
        TestResultHandlerInterface $testResultContainer,
        string $status
    ) {
        $this->testResultContainer = $testResultContainer;
        $this->status = $status;
    }

    /**
     * {@inheritdoc}
     */
    public function handleLogItem(AbstractParaunitProcess $process, Log $logItem): ?TestResultInterface
    {
        if ($this->logMatches($logItem)) {
            $testResult = $this->createFromLog($logItem);
            $this->testResultContainer->handleTestResult($process, $testResult);
            $process->setWaitingForTestResult(false);

            return $testResult;
        }

        return null;
    }

    protected function logMatches(Log $log): bool
    {
        return $log->getStatus() === $this->status;
    }

    private function createFromLog(Log $logItem): TestResultInterface
    {
        $message = $logItem->getMessage();

        if ($message) {
            return new TestResultWithMessage($logItem->getTest(), $message);
        }

        return new MuteTestResult($logItem->getTest());
    }
}
