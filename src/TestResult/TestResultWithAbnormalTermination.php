<?php

declare(strict_types=1);

namespace Paraunit\TestResult;

use Paraunit\Parser\ValueObject\Test;

class TestResultWithAbnormalTermination extends TestResultWithMessage
{
    private string $testOutput = '';

    public function __construct(Test $test)
    {
        parent::__construct($test, 'Possible abnormal termination, last test was ' . $test->name);
    }

    public function getFailureMessage(): string
    {
        return parent::getFailureMessage() . "\n" . $this->testOutput;
    }

    public function setTestOutput(string $testOutput): void
    {
        $this->testOutput = $testOutput;
    }
}
