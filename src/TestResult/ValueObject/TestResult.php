<?php

declare(strict_types=1);

namespace Paraunit\TestResult\ValueObject;

use Paraunit\Logs\ValueObject\LogData;
use Paraunit\Logs\ValueObject\Test;
use Paraunit\TestResult\TestResultWithMessage;

class TestResult
{
    public function __construct(
        public readonly Test $test,
        public readonly PrintableTestStatus $status,
    ) {
    }

    public static function from(LogData $log): self
    {
        if ($log->message) {
            return new TestResultWithMessage($log->test, $log->status->toTestStatus(), $log->message);
        }

        return new self($log->test, $log->status->toTestStatus());
    }
}
