<?php

namespace Paraunit\Parser;

use Paraunit\Process\ProcessWithResultsInterface;
use Paraunit\TestResult\NullTestResult;

/**
 * Class TestStartParser
 * @package Paraunit\Parser
 */
class TestStartParser implements JSONParserChainElementInterface
{
    const UNKNOWN_FUNCTION = 'UNKNOWN -- log not found';

    /** @var ProcessWithResultsInterface */
    private $lastProcess;

    /** @var string */
    private $lastFunction;

    public function handleLogItem(ProcessWithResultsInterface $process, \stdClass $logItem)
    {
        if (property_exists($logItem, 'status') && $logItem->status == JSONLogFetcher::LOG_ENDING_STATUS) {
            return $this->handleLogTermination($process, $logItem);
        }

        if (property_exists($logItem, 'event')) {
            if ($logItem->event == 'testStart' || $logItem->event == 'suiteStart') {
                $process->setWaitingForTestResult(true);
                $this->saveProcessFunction($process, $logItem);

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

        if (is_null($this->lastFunction) || $process !== $this->lastProcess) {
            $logItem->test = self::UNKNOWN_FUNCTION;
        }
    }
}
