<?php

declare(strict_types=1);

namespace Paraunit\Runner;

use Paraunit\TestResult\ValueObject\TestIssue;
use Paraunit\TestResult\ValueObject\TestOutcome;

class RunnerConfiguration
{
    /** @var array<value-of<TestIssue|TestOutcome>, bool> */
    private array $stopOn = [];

    public function shouldStopOn(TestIssue|TestOutcome $result): bool
    {
        return $this->stopOn[$result->value] ?? false;
    }

    public function setStopOn(TestIssue|TestOutcome $result, bool $willStop = true): void
    {
        $this->stopOn[$result->value] = $willStop;
    }
}
