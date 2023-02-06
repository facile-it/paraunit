<?php

declare(strict_types=1);

namespace Paraunit\Lifecycle;

use Paraunit\Logs\ValueObject\Test;
use Paraunit\TestResult\ValueObject\TestIssue;
use Paraunit\TestResult\ValueObject\TestOutcome;

class TestCompleted extends AbstractEvent
{
    public function __construct(
        public readonly Test $test,
        public readonly TestOutcome|TestIssue $outcome,
    ) {
    }
}
