<?php

namespace Paraunit\TestResult;

/**
 * Class NullTestResult
 * @package Paraunit\TestResult
 */
class NullTestResult implements TestResultInterface
{
    /** @var  TestResultFormat */
    private $testResultFormat;

    /**
     * AbstractTestResult constructor.
     */
    public function __construct()
    {
        $this->testResultFormat = new TestResultFormat('?', 'null', '');
    }

    /**
     * @param TestResultFormat $testResultFormat
     */
    public function setTestResultFormat(TestResultFormat $testResultFormat)
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
}
