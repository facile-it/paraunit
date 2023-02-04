<?php

declare(strict_types=1);

namespace Paraunit\TestResult;

use Paraunit\Logs\ValueObject\LogData;
use Paraunit\Logs\ValueObject\Test;
use Paraunit\TestResult\ValueObject\TestOutcome;

class TestResult
{
    public function __construct(
        public readonly Test $test,
        public readonly TestOutcome $outcome,
    ) {
    }

    public static function from(LogData $log): self
    {
        if ($log->message) {
            return new TestResultWithMessage($log->test, TestOutcome::fromStatus($log->status), $log->message);
        }

        return new self($log->test, TestOutcome::fromStatus($log->status));
    }

    public function isMoreImportantThan(?self $other): bool
    {
        if ($other === null) {
            return true;
        }

        // TODO - more complex logic
        return $other->outcome === TestOutcome::Passed;
    }
}
