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
    public function handleLogItem(ProcessWithResultsInterface $process, \stdClass $logItem)
    {
        switch($logItem->status) {
            case 'testStart':
            case 'suiteStart':
                $process->setWaitingForTestResult(true);
                return new NullTestResult();
            default:
                return null;
        }
    }
}
