<?php

declare(strict_types=1);

namespace Paraunit\TestResult;

use Paraunit\Parser\ValueObject\Test;
use Paraunit\Parser\ValueObject\TestStatus;
use Paraunit\TestResult\Interfaces\FailureMessageInterface;

class TestResultWithMessage extends TestResult
{
    public function __construct(Test $test, TestStatus $status, public readonly string $message)
    {
        parent::__construct($test, $status);
    }
}
