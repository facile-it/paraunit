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
        switch($logItem->status) {
            case 'testStart':
            case 'suiteStart':
                $process->setWaitingForTestResult(true);
                $this->saveProcessFunction($process, $logItem);

                return new NullTestResult();
            case JSONLogFetcher::LOG_ENDING_STATUS:
                if ($process->isWaitingForTestResult()) {
                    $this->injectLastFunctionInEndingLog($process, $logItem);

                    return null;
                } else {
                    return new NullTestResult();
                }
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
