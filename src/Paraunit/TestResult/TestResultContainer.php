<?php

namespace Paraunit\TestResult;

/**
 * Class TestResultContainer
 * @package Paraunit\TestResult
 */
class TestResultContainer implements TestResultContainerInterface
{
    /** @var  TestResultInterface[] */
    private $testResults;

    /** @var  TestResultFormat */
    private $testResultFormat;

    /**
     * TestResultContainer constructor.
     * @param TestResultFormat $testResultFormat
     */
    public function __construct(TestResultFormat $testResultFormat)
    {
        $this->testResultFormat = $testResultFormat;
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
    public function addTestResult(TestResultInterface $testResult)
    {
        $this->testResults[] = $testResult;
    }
}
