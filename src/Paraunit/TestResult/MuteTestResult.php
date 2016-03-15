<?php

namespace Paraunit\TestResult;

/**
 * Class MuteTestResult
 * @package Paraunit\Output\MuteTestResult
 */
class MuteTestResult extends NullTestResult implements TestResultInterface
{
    /**
     * MuteTestResult constructor.
     * @param TestResultFormat $testResultFormat
     */
    public function __construct(TestResultFormat $testResultFormat)
    {
        $this->setTestResultFormat($testResultFormat);
    }
}
