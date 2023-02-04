<?php

declare(strict_types=1);

namespace Paraunit\TestResult;

use Paraunit\Logs\ValueObject\Test;
use Paraunit\Printer\ValueObject\TestOutcome;

class TestResultWithMessage extends TestResult
{
    public function __construct(Test $test, TestOutcome $outcome, public readonly string $message)
    {
        parent::__construct($test, $outcome);
    }
}
