<?php

declare(strict_types=1);

namespace Paraunit\TestResult;

use Paraunit\Logs\ValueObject\Test;
use Paraunit\TestResult\ValueObject\PrintableTestStatus;
use Paraunit\TestResult\ValueObject\TestResult;

class TestResultWithMessage extends TestResult
{
    public function __construct(Test $test, PrintableTestStatus $status, public readonly string $message)
    {
        parent::__construct($test, $status);
    }
}
