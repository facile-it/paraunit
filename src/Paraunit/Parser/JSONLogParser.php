<?php

namespace Paraunit\Parser;

use Paraunit\Exception\JSONLogNotFoundException;
use Paraunit\Lifecycle\ProcessEvent;
use Paraunit\Process\ParaunitProcessAbstract;
use Paraunit\TestResult\TestResultContainerBearerInterface;
use Paraunit\TestResult\TestResultContainerInterface;
use Paraunit\TestResult\TestResultInterface;
use Paraunit\TestResult\TestResultWithMessage;

/**
 * Class JSONLogParser
 * @package Paraunit\Parser
 */
class JSONLogParser implements TestResultContainerBearerInterface
{
    /** @var  JSONLogFetcher */
    private $logLocator;

    /** @var JSONParserChainElementInterface[] */
    private $parsers;

    /** @var  TestResultContainerInterface */
    private $abnormalTerminatedTestResultContainer;

    /**
     * JSONLogParser constructor.
     * @param JSONLogFetcher $logLocator
     * @param TestResultContainerInterface $abnormalTerminatedTestResultContainer
     */
    public function __construct(JSONLogFetcher $logLocator, TestResultContainerInterface $abnormalTerminatedTestResultContainer)
    {
        $this->logLocator = $logLocator;
        $this->abnormalTerminatedTestResultContainer = $abnormalTerminatedTestResultContainer;
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
     * @return TestResultContainerInterface
     */
    public function getTestResultContainer()
    {
        return $this->getAbnormalTerminatedTestResultContainer();
    }

    /**
     * @return TestResultContainerInterface
     */
    public function getAbnormalTerminatedTestResultContainer()
    {
        return $this->abnormalTerminatedTestResultContainer;
    }

    /**
     * @param ProcessEvent $processEvent
     */
    public function onProcessTerminated(ProcessEvent $processEvent)
    {
        $process = $processEvent->getProcess();

        try {
            $logs = $this->logLocator->fetch($process);
        } catch (JSONLogNotFoundException $exception) {
            $this->reportAbnormalTermination($process);

            return;
        }

        $stillExpectingTestResult = false;

        foreach ($logs as $singleLog) {
            $stillExpectingTestResult = true;

            if ($singleLog->event == 'test') {
                $stillExpectingTestResult = ! $this->testResultFound($process, $singleLog);
            }
        }

        if ($stillExpectingTestResult) {
            /** @var \stdClass $singleLog No issue here: the if is true only if the foreach went at least once */
            $this->reportAbnormalTermination($process, $singleLog->test);
        }
    }

    /**
     * @param ParaunitProcessAbstract $process
     * @param \stdClass $logItem
     * @return bool False if the parsing is still waiting for a test to give results
     */
    private function testResultFound(ParaunitProcessAbstract $process, \stdClass $logItem)
    {
        foreach ($this->parsers as $parser) {
            if ($parser->parseLog($process, $logItem) instanceof TestResultInterface) {
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
        $testResult = new TestResultWithMessage('X', $culpableFunctionName, "Complete test output:\n\n" . $process->getOutput());
        $process->reportAbnormalTermination($testResult);
        $this->abnormalTerminatedTestResultContainer->addTestResult($testResult);
    }
}
