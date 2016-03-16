<?php

namespace Paraunit\Parser;

use Paraunit\Exception\JSONLogNotFoundException;
use Paraunit\Lifecycle\ProcessEvent;
use Paraunit\Process\AbstractParaunitProcess;
use Paraunit\TestResult\Interfaces\TestResultContainerInterface;
use Paraunit\TestResult\Interfaces\TestResultInterface;

/**
 * Class JSONLogParser
 * @package Paraunit\Parser
 */
class JSONLogParser
{
    /** @var  JSONLogFetcher */
    private $logLocator;

    /** @var  JSONParserChainElementInterface[] */
    private $parsers;

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
     * @param JSONParserChainElementInterface $container
     */
    public function addParser(JSONParserChainElementInterface $container)
    {
        $this->parsers[] = $container;
    }

    /**
     * @return TestResultContainerInterface[]
     */
    public function getParsers()
    {
        return $this->parsers;
    }

    /**
     * @return TestResultContainerInterface[]
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
        $process = $processEvent->getProcess();
        $logs = $this->logLocator->fetch($process);

        foreach ($logs as $singleLog) {
            $this->processLog($process, $singleLog);
        }
    }

    /**
     * @param AbstractParaunitProcess $process
     * @param \stdClass $logItem
     */
    private function processLog(AbstractParaunitProcess $process, \stdClass $logItem)
    {
        /** @var JSONParserChainElementInterface $resultContainer */
        foreach ($this->parsers as $resultContainer) {
            if ($resultContainer->handleLogItem($process, $logItem) instanceof TestResultInterface) {
                return;
            }
        }
    }
}
