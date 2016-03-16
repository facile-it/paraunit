<?php

namespace Paraunit\TestResult;

/**
 * Class TestResultWithAbnormalTermination
 * @package Paraunit\TestResult
 */
class TestResultWithAbnormalTermination extends TestResultWithMessage
{
    /** @var string */
    private $testOutput;

    /**
     * @return string
     */
    public function getFailureMessage()
    {
        return parent::getFailureMessage() . "\n" . $this->testOutput;
    }

    /**
     * @return string
     */
    public function getTestOutput()
    {
        return $this->testOutput;
    }

    /**
     * @param string $testOutput
     */
    public function setTestOutput($testOutput)
    {
        $this->testOutput = $testOutput;
    }
}
