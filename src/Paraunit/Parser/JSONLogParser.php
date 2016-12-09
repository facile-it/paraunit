<?php

namespace Paraunit\Parser;

use Paraunit\Lifecycle\ProcessEvent;
use Paraunit\Process\AbstractParaunitProcess;
use Paraunit\TestResult\Interfaces\TestResultBearerInterface;
use Paraunit\TestResult\Interfaces\TestResultHandlerInterface;
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

    /** @var TestResultHandlerInterface */
    private $noTestExecutedResultContainer;

    /**
     * JSONLogParser constructor.
     * @param JSONLogFetcher $logLocator
     * @param TestResultHandlerInterface $noTestExecutedResultContainer
     */
    public function __construct(JSONLogFetcher $logLocator, TestResultHandlerInterface $noTestExecutedResultContainer)
    {
        $this->logLocator = $logLocator;
        $this->noTestExecutedResultContainer = $noTestExecutedResultContainer;
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
     * @return TestResultBearerInterface[]
     */
    public function getParsers()
    {
        return $this->parsers;
    }

    /**
     * @param ProcessEvent $processEvent
     */
    public function onProcessTerminated(ProcessEvent $processEvent)
    {
        $process = $processEvent->getProcess();
        $logs = $this->logLocator->fetch($process);

        if ($this->noTestsExecuted($process, $logs)) {
            $this->noTestExecutedResultContainer->addProcessToFilenames($process);

            return;
        }

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

    /**
     * @param AbstractParaunitProcess $process
     * @param array $logs
     * @return bool
     */
    private function noTestsExecuted(AbstractParaunitProcess $process, array $logs)
    {
        return $process->getExitCode() === 0 && count($logs) === 1;
    }
}
