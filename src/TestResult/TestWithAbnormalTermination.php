<?php

declare(strict_types=1);

namespace Paraunit\TestResult;

use Paraunit\Logs\ValueObject\Test;
use Paraunit\Logs\ValueObject\TestStatus;
use Paraunit\Process\AbstractParaunitProcess;

class TestWithAbnormalTermination extends TestResultWithMessage
{
    public readonly string $testOutput;

    public function __construct(Test $test, AbstractParaunitProcess $process)
    {
        parent::__construct($test, TestStatus::Unknown, 'Possible abnormal termination, last prepared test was ' . $test->name);
        $this->testOutput = $process->getOutput();
    }
}
