<?php

namespace Paraunit\Parser;

use Paraunit\Exception\JSONLogNotFoundException;
use Paraunit\Process\ProcessResultInterface;

/**
 * Class JSONLogParser
 * @package Paraunit\Parser
 */
class JSONLogParser implements ProcessOutputParserChainElementInterface
{
    /** @var  JSONLogFetcher */
    private $logLocator;

    /**
     * JSONLogParser constructor.
     * @param JSONLogFetcher $logLocator
     */
    public function __construct(JSONLogFetcher $logLocator)
    {
        $this->logLocator = $logLocator;
    }

    public function parseAndContinue(ProcessResultInterface $process)
    {
        try {
            $logs = json_decode($this->logLocator->fetch($process));
        } catch (JSONLogNotFoundException $exception) {
            return true;
        }

        foreach ($logs as $singleLog) {
            $this->handleLogItem($process, $singleLog);
        }

        return false;
    }

    /**
     * @param ProcessResultInterface $process
     * @param \stdClass $logItem
     */
    private function handleLogItem(ProcessResultInterface $process, \stdClass $logItem)
    {
        if ($logItem->event == 'test') {
            switch ($logItem->status) {
                case 'pass':
                    $process->addTestResult('.');
                    break;
                case 'fail':
                    $process->addTestResult('F');
                    break;
                case 'error':
                    $process->addTestResult($this->checkErrorMessage($logItem));
                    break;
                case 'warning':
                    $process->addTestResult('W');
                    break;
                default:
                    var_dump($logItem->status);
            }
        }
    }

    private function checkErrorMessage(\stdClass $logItem)
    {
        switch (true) {
            case preg_match('/^Incomplete Test: /', $logItem->message):
                return 'I';
            case preg_match('/^Skipped Test: /', $logItem->message):
                return 'S';
            case preg_match('/^Risky Test: /', $logItem->message):
                return 'R';
            default:
                return 'E';
        }
    }
}
