<?php

namespace Paraunit\Parser;

use Paraunit\Exception\JSONLogNotFoundException;
use Paraunit\Exception\RecoverableTestErrorException;
use Paraunit\Lifecycle\ProcessEvent;
use Paraunit\Process\ParaunitProcessAbstract;
use Paraunit\Process\ProcessResultInterface;

/**
 * Class JSONLogParser
 * @package Paraunit\Parser
 */
class JSONLogParser
{
    /** @var  JSONLogFetcher */
    private $logLocator;

    /** @var JSONParserChainElementInterface[] */
    protected $parsers;

    /**
     * JSONLogParser constructor.
     * @param JSONLogFetcher $logLocator
     */
    public function __construct(JSONLogFetcher $logLocator)
    {
        $this->logLocator = $logLocator;
        $this->parsers = array();
    }

    /**
     * @param JSONParserChainElementInterface $parser
     */
    public function addParser(JSONParserChainElementInterface $parser)
    {
        $this->parsers[] = $parser;
    }

    /**
     * @return JSONParserChainElementInterface[]
     */
    public function getParsersForPrinting()
    {
        return array_reverse($this->parsers);
    }

    /**
     * @param ProcessEvent $processEvent
     */
    public function onProcessTerminated(ProcessEvent $processEvent)
    {
        $this->parse($processEvent->getProcess());
    }

    /**
     * @param ParaunitProcessAbstract $process
     */
    public function parse(ParaunitProcessAbstract $process)
    {
        try {
            $logs = $this->logLocator->fetch($process);
        } catch (JSONLogNotFoundException $exception) {
            $process->addTestResult('X');
            $process->reportAbnormalTerminationInFunction('Unknown function -- test log not found');

            return;
        }

        $expectingTestResult = false;

        foreach ($logs as $singleLog) {
            if ($singleLog->event == 'test') {
                $expectingTestResult = false;
                $this->extractTestResult($process, $singleLog);
            } else {
                $expectingTestResult = true;
            }
        }

        if ($expectingTestResult) {
            $process->addTestResult('X');
            $process->reportAbnormalTerminationInFunction($singleLog->test);
        }
    }

    /**
     * @param ProcessResultInterface $process
     * @param \stdClass $logItem
     * @return bool False if the parsing is still waiting for a test to give results
     */
    private function extractTestResult(ProcessResultInterface $process, \stdClass $logItem)
    {
        foreach ($this->parsers as $parser) {
            if ($parser->parsingFoundResult($process, $logItem)) {
                return true;
            }
        }

        return false;
    }
}
