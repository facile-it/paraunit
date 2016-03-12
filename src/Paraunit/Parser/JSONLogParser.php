<?php

namespace Paraunit\Parser;

use Paraunit\Exception\JSONLogNotFoundException;
use Paraunit\Exception\RecoverableTestErrorException;
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

    public function parse(ProcessResultInterface $process)
    {
        $logs = json_decode($this->logLocator->fetch($process));

        foreach ($logs as $singleLog) {
            $this->extractTestResult($process, $singleLog);
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
