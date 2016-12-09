<?php

namespace Paraunit\TestResult;

use Paraunit\TestResult\Interfaces\PrintableTestResultInterface;

/**
 * Class MuteTestResult
 * @package Paraunit\Output\MuteTestResult
 */
class MuteTestResult extends NullTestResult implements PrintableTestResultInterface
{
    /** @var  TestResultFormat */
    private $testResultFormat;

    /**
     * AbstractTestResult constructor.
     */
    public function __construct()
    {
        $this->testResultFormat = new TestResultFormat('null', '');
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
