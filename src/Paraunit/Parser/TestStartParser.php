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
    /** @var  ProcessWithResultsInterface */
    private $lastProcess;

    /** @var  string */
    private $lastFunction;

    public function handleLogItem(ProcessWithResultsInterface $process, \stdClass $logItem)
    {
        switch($logItem->status) {
            case 'testStart':
                $this->lastFunction = $logItem->test;
                $this->lastProcess = $process;
                return new NullTestResult();
            case 'suiteStart':
                return new NullTestResult();
            default:
                return null;
        }
    }

    /**
     * @param ProcessWithResultsInterface $process
     * @return null|string
     */
    public function getLastFunctionByProcess(ProcessWithResultsInterface $process)
    {
        if ($process === $this->lastProcess) {
            return $this->lastFunction;
        }

        return null;
    }
}
