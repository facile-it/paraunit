<?php

declare(strict_types=1);

namespace Paraunit\Printer\ValueObject;

enum OutputStyle: string
{
    case Ok = 'ok';
    case Skip = 'skip';
    case Warning = 'warning';
    case Incomplete = 'incomplete';
    case Error = 'error';
    case Abnormal = 'abnormal';

    public static function fromOutcome(TestOutcome $outcome): self
    {
        return match ($outcome) {
            TestOutcome::AbnormalTermination,
            TestOutcome::CoverageFailure => self::Abnormal,
            TestOutcome::Error,
            TestOutcome::Failure => self::Error,
            TestOutcome::Warning,
            TestOutcome::Deprecation,
            TestOutcome::Risky,
            TestOutcome::NoTestExecuted => self::Warning,
            TestOutcome::Skipped => self::Skip,
            TestOutcome::Incomplete => self::Incomplete,
            TestOutcome::Retry,
            TestOutcome::Passed => self::Ok,
        };
    }
}
