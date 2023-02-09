<?php

declare(strict_types=1);

namespace Paraunit\TestResult;

use Paraunit\Logs\ValueObject\Test;
use Paraunit\Process\Process;
use Paraunit\TestResult\ValueObject\TestOutcome;

class TestWithAbnormalTermination extends TestResultWithMessage
{
    public function __construct(Test $test, Process $process)
    {
        parent::__construct(
            $test,
            TestOutcome::AbnormalTermination,
            'Possible abnormal termination, last prepared test was '
            . $test->name
            . PHP_EOL . PHP_EOL
            . $process->getOutput()
        );
    }
}
