<?php

namespace Paraunit\TestResult;
use Paraunit\Parser\JSONParserChainElementInterface;
use Paraunit\Process\ProcessWithResultsInterface;

/**
 * Class TestResultContainer
 * @package Paraunit\TestResult
 */
class TestResultContainer implements TestResultContainerInterface, JSONParserChainElementInterface
{
    /** @var JSONParserChainElementInterface */
    private $parser;

    /** @var  TestResultFormat */
    private $testResultFormat;

    /** @var  TestResultInterface[] */
    private $testResults;

    /**
     * TestResultContainer constructor.
     * @param JSONParserChainElementInterface $parser
     * @param TestResultFormat $testResultFormat
     */
    public function __construct(JSONParserChainElementInterface $parser, TestResultFormat $testResultFormat)
    {
        $this->parser = $parser;
        $this->testResultFormat = $testResultFormat;
    }

    /**
     * @param ProcessWithResultsInterface $process
     * @param \stdClass $logItem
     * @return null|TestResultInterface A result is returned when identified (and the chain needs to stop)
     */
    public function handleLogItem(ProcessWithResultsInterface $process, \stdClass $logItem)
    {
        $result = $this->parser->handleLogItem($process, $logItem);

        if ($result instanceof TestResultInterface) {
            $this->addTestResult($result);
            $process->addTestResult($result);

            return $result;
        }

        return null;
    }

    /**
     * @return TestResultFormat
     */
    public function getTestResultFormat()
    {
        return $this->testResultFormat;
    }

    /**
     * @return TestResultInterface[]
     */
    public function getTestResults()
    {
        return $this->testResults;
    }

    /**
     * @param TestResultInterface $testResult
     */
    protected function addTestResult(TestResultInterface $testResult)
    {
        $this->testResults[] = $testResult;
    }
}
