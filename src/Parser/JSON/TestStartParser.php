<?php

declare(strict_types=1);

namespace Paraunit\Parser\JSON;

use Paraunit\Process\AbstractParaunitProcess;
use Paraunit\TestResult\Interfaces\TestResultInterface;
use Paraunit\TestResult\NullTestResult;

class TestStartParser implements ParserChainElementInterface
{
    private const UNKNOWN_FUNCTION = 'UNKNOWN -- log not found';

    /** @var AbstractParaunitProcess */
    private $lastProcess;

    /** @var string */
    private $lastFunction;

    public function handleLogItem(AbstractParaunitProcess $process, Log $logItem): ?TestResultInterface
    {
        if ($logItem->getStatus() === LogFetcher::LOG_ENDING_STATUS) {
            return $this->handleLogTermination($process, $logItem);
        }

        if (property_exists($logItem, 'event')) {
            if ($logItem->event === 'testStart' || $logItem->event === 'suiteStart') {
                $process->setWaitingForTestResult(true);
                $this->saveProcessFunction($process, $logItem);
                $this->saveTestFQCN($process, $logItem);

                return new NullTestResult();
            }
        }

        return null;
    }

    private function handleLogTermination(AbstractParaunitProcess $process, \stdClass $logItem): ?NullTestResult
    {
        if ($process->isWaitingForTestResult()) {
            $this->injectLastFunctionInEndingLog($process, $logItem);

            return null;
        }

        return new NullTestResult();
    }

    private function saveProcessFunction(AbstractParaunitProcess $process, \stdClass $logItem): void
    {
        $this->lastProcess = $process;
        $this->lastFunction = property_exists($logItem, 'test') ? $logItem->test : self::UNKNOWN_FUNCTION;
    }

    private function injectLastFunctionInEndingLog(AbstractParaunitProcess $process, \stdClass $logItem): void
    {
        $logItem->test = $this->lastFunction;

        if ($this->lastFunction === null || $process !== $this->lastProcess) {
            $logItem->test = self::UNKNOWN_FUNCTION;
        }
    }

    private function saveTestFQCN(AbstractParaunitProcess $process, \stdClass $logItem): void
    {
        if ($process->getTestClassName()) {
            return;
        }

        if (! property_exists($logItem, 'suite')) {
            return;
        }

        if (! property_exists($logItem, 'test')) {
            return;
        }

        $suiteName = explode('::', $logItem->suite);
        $process->setTestClassName($suiteName[0]);
    }
}
