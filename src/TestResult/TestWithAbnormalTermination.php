<?php

declare(strict_types=1);

namespace Paraunit\TestResult;

use Paraunit\Parser\ValueObject\Test;
use Paraunit\Parser\ValueObject\TestStatus;
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
