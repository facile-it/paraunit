<?php

declare(strict_types=1);

namespace Paraunit\Printer\ValueObject;

use Paraunit\TestResult\ValueObject\TestIssue;
use Paraunit\TestResult\ValueObject\TestOutcome;

enum OutputStyle: string
{
    case Ok = 'ok';
    case Skip = 'skip';
    case Warning = 'warning';
    case Incomplete = 'incomplete';
    case Error = 'error';
    case Abnormal = 'abnormal';

    public static function fromStatus(TestOutcome|TestIssue $status): self
    {
        return match ($status) {
            TestOutcome::AbnormalTermination,
            TestIssue::CoverageFailure => self::Abnormal,
            TestOutcome::Error,
            TestOutcome::Failure => self::Error,
            TestIssue::Warning,
            TestIssue::Deprecation,
            TestIssue::Risky,
            TestOutcome::NoTestExecuted => self::Warning,
            TestOutcome::Skipped => self::Skip,
            TestOutcome::Incomplete => self::Incomplete,
            TestOutcome::Retry,
            TestOutcome::Passed => self::Ok,
        };
    }
}
