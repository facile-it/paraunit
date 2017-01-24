<?php

namespace Paraunit\Parser\JSON;

use Paraunit\Process\ProcessWithResultsInterface;
use Paraunit\TestResult\NullTestResult;

/**
 * Class TestStartParser
 * @package Paraunit\Parser\JSON
 */
class TestStartParser implements ParserChainElementInterface
{
    const UNKNOWN_FUNCTION = 'UNKNOWN -- log not found';

    /** @var ProcessWithResultsInterface */
    private $lastProcess;

    /** @var string */
    private $lastFunction;

    public function handleLogItem(ProcessWithResultsInterface $process, \stdClass $logItem)
    {
        if (property_exists($logItem, 'status') && $logItem->status === LogFetcher::LOG_ENDING_STATUS) {
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

    /**
     * @param ProcessWithResultsInterface $process
     * @param \stdClass $logItem
     * @return null|NullTestResult
     */
    private function handleLogTermination(ProcessWithResultsInterface $process, \stdClass $logItem)
    {
        if ($process->isWaitingForTestResult()) {
            $this->injectLastFunctionInEndingLog($process, $logItem);

            return null;
        }

        return new NullTestResult();
    }

    /**
     * @param ProcessWithResultsInterface $process
     * @param \stdClass $logItem
     */
    private function saveProcessFunction(ProcessWithResultsInterface $process, \stdClass $logItem)
    {
        $this->lastProcess = $process;
        $this->lastFunction = property_exists($logItem, 'test') ? $logItem->test : self::UNKNOWN_FUNCTION;
    }

    /**
     * @param ProcessWithResultsInterface $process
     * @param \stdClass $logItem
     */
    private function injectLastFunctionInEndingLog(ProcessWithResultsInterface $process, \stdClass $logItem)
    {
        $logItem->test = $this->lastFunction;

        if ($this->lastFunction === null || $process !== $this->lastProcess) {
            $logItem->test = self::UNKNOWN_FUNCTION;
        }
    }

    private function saveTestFQCN(ProcessWithResultsInterface $process, \stdClass $logItem)
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
