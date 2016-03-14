<?php

namespace Paraunit\Parser;

use Paraunit\Exception\JSONLogNotFoundException;
use Paraunit\Lifecycle\ProcessEvent;
use Paraunit\Output\OutputContainerInterface;
use Paraunit\Process\ParaunitProcessAbstract;
use Paraunit\Process\ProcessResultInterface;

/**
 * Class JSONLogParser
 * @package Paraunit\Parser
 */
class JSONLogParser implements OutputContainerBearerInterface
{
    /** @var  JSONLogFetcher */
    private $logLocator;

    /** @var JSONParserChainElementInterface[] */
    private $parsers;

    /** @var  OutputContainerInterface */
    private $abnormalTerminatedOutputContainer;

    /**
     * JSONLogParser constructor.
     * @param JSONLogFetcher $logLocator
     * @param OutputContainerInterface $abnormalTerminatedOutputContainer
     */
    public function __construct(JSONLogFetcher $logLocator, OutputContainerInterface $abnormalTerminatedOutputContainer)
    {
        $this->logLocator = $logLocator;
        $this->abnormalTerminatedOutputContainer = $abnormalTerminatedOutputContainer;
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
     * @return OutputContainerInterface
     */
    public function getOutputContainer()
    {
        return $this->getAbnormalTerminatedOutputContainer();
    }

    /**
     * @return OutputContainerInterface
     */
    public function getAbnormalTerminatedOutputContainer()
    {
        return $this->abnormalTerminatedOutputContainer;
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
            $this->reportAbnormalTermination($process);

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
            $this->reportAbnormalTermination($process, $singleLog->test);
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

    /**
     * @param ParaunitProcessAbstract $process
     * @param string $culpableFunctionName
     */
    private function reportAbnormalTermination(ParaunitProcessAbstract $process, $culpableFunctionName = 'Unknown function -- test log not found')
    {
        $process->addTestResult('X');
        $process->reportAbnormalTermination();
        $this->getAbnormalTerminatedOutputContainer()->addToOutputBuffer(
            $process,
            'Culpable test function: ' . $culpableFunctionName . " -- complete test output:\n\n" . $process->getOutput()
        );
    }
}
