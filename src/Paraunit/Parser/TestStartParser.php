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
    /** @var ProcessWithResultsInterface */
    private $lastProcess;

    /** @var string */
    private $lastFunction;

    public function handleLogItem(ProcessWithResultsInterface $process, \stdClass $logItem)
    {
        switch($logItem->status) {
            case 'testStart':
            case 'suiteStart':
                $process->setWaitingForTestResult(true);
                $this->saveProcessFunction($process, $logItem);

                return new NullTestResult();
            case JSONLogFetcher::LOG_ENDING_STATUS:
                $this->injectLastFunctionInEndingLog($process, $logItem);
            default:
                return null;
        }
    }

    /**
     * @param ProcessWithResultsInterface $process
     * @param \stdClass $logItem
     */
    private function saveProcessFunction(ProcessWithResultsInterface $process, \stdClass $logItem)
    {
        $this->lastProcess = $process;
        $this->lastFunction = property_exists($logItem, 'test') ? $logItem->test : null;
    }

    /**
     * @param ProcessWithResultsInterface $process
     * @param \stdClass $logItem
     */
    private function injectLastFunctionInEndingLog(ProcessWithResultsInterface $process, \stdClass $logItem)
    {
        if (is_null($this->lastProcess) || $process === $this->lastProcess) {
            $logItem->test = $this->lastFunction ?: 'UNKNOWN -- log not found';
        }
    }
}
