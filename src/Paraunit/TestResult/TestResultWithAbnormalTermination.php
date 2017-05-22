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

    public function getFailureMessage(): string
    {
        return parent::getFailureMessage() . "\n" . $this->testOutput;
    }

    public function getTestOutput(): string
    {
        return $this->testOutput;
    }

    public function setTestOutput(string $testOutput)
    {
        $this->testOutput = $testOutput;
    }
}
