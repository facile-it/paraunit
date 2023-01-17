<?php

declare(strict_types=1);

namespace Paraunit\TestResult;

use Paraunit\Logs\ValueObject\Test;
use Paraunit\Logs\ValueObject\TestStatus;

class TestResultWithMessage extends TestResult
{
    public function __construct(Test $test, TestStatus $status, public readonly string $message)
    {
        parent::__construct($test, $status);
    }
}
