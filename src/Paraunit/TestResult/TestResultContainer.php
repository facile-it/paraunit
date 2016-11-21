<?php

namespace Paraunit\TestResult;

use Paraunit\Parser\JSONParserChainElementInterface;
use Paraunit\Process\OutputAwareInterface;
use Paraunit\Process\ProcessWithResultsInterface;
use Paraunit\TestResult\Interfaces\PrintableTestResultInterface;
use Paraunit\TestResult\Interfaces\TestResultBearerInterface;
use Paraunit\TestResult\Interfaces\TestResultInterface;

/**
 * Class TestResultContainer
 * @package Paraunit\TestResult
 */
class TestResultContainer extends DumbTestResultContainer implements TestResultBearerInterface, JSONParserChainElementInterface
{
    /** @var JSONParserChainElementInterface */
    private $parser;

    /** @var  PrintableTestResultInterface[] */
    private $testResults;

    /**
     * TestResultContainer constructor.
     * @param JSONParserChainElementInterface $parser
     * @param TestResultFormat $testResultFormat
     */
    public function __construct(JSONParserChainElementInterface $parser, TestResultFormat $testResultFormat)
    {
        parent::__construct($testResultFormat);
        $this->parser = $parser;
        $this->testResults = array();
    }

    /**
     * @param ProcessWithResultsInterface $process
     * @param \stdClass $logItem
     * @return null|TestResultInterface|PrintableTestResultInterface Returned when the chain needs to stop
     */
    public function handleLogItem(ProcessWithResultsInterface $process, \stdClass $logItem)
    {
        $result = $this->parser->handleLogItem($process, $logItem);

        if ($result instanceof TestResultWithAbnormalTermination && $process instanceof OutputAwareInterface) {
            $this->addProcessOutputToResult($result, $process);
        }

        if ($result instanceof PrintableTestResultInterface) {
            $result->setTestResultFormat($this->testResultFormat);
            $this->testResults[] = $result;

            $this->addProcessToFilenames($process);
            $process->addTestResult($result);
        }

        return $result;
    }

    /**
     * @param TestResultWithAbnormalTermination $result
     * @param OutputAwareInterface $process
     */
    private function addProcessOutputToResult(TestResultWithAbnormalTermination $result, OutputAwareInterface $process)
    {
        $tag = $this->testResultFormat->getTag();
        $output = $process->getOutput() ?: sprintf('<%s><[NO OUTPUT FOUND]></%s>', $tag, $tag);
        $result->setTestOutput($output);
    }

    /**
     * @return PrintableTestResultInterface[]
     */
    public function getTestResults()
    {
        return $this->testResults;
    }

    /**
     * @return int
     */
    public function countTestResults()
    {
        return count($this->testResults);
    }
}
