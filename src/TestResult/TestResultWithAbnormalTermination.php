<?php

declare(strict_types=1);

namespace Paraunit\TestResult;

class TestResultWithAbnormalTermination extends TestResultWithMessage
{
    /** @var string */
    private $testOutput;

    public function __construct(string $testName)
    {
        parent::__construct($testName, 'Possible abnormal termination, last test was ' . $testName);
    }

    public function getFailureMessage(): string
    {
        return parent::getFailureMessage() . "\n" . $this->testOutput;
    }

    public function getTestOutput(): string
    {
        return $this->testOutput;
    }

    public function setTestOutput(string $testOutput): void
    {
        $this->testOutput = $testOutput;
    }
}
