<?php

declare(strict_types=1);

namespace Paraunit\TestResult;

use Paraunit\Logs\ValueObject\LogData;
use Paraunit\Logs\ValueObject\Test;
use Paraunit\Logs\ValueObject\TestStatus;

class TestResult
{
    public function __construct(
        public readonly Test $test,
        public readonly TestStatus $status,
    ) {
    }

    public static function from(LogData $log): self
    {
        if ($log->message) {
            return new TestResultWithMessage($log->test, $log->status, $log->message);
        }

        return new self($log->test, $log->status);
    }

    public function isMoreImportantThan(self $other): bool
    {
        // TODO - more complex logic
        return $this->status === TestStatus::Passed;
    }
}
