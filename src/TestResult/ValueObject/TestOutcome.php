<?php

declare(strict_types=1);

namespace Paraunit\TestResult\ValueObject;

use Paraunit\Logs\ValueObject\TestStatus;

enum TestOutcome: string implements PrintableTestStatus
{
    case AbnormalTermination = 'AbnormalTermination';
    case Error = 'Error';
    case Failure = 'Failure';
    case NoTestExecuted = 'NoTestExecuted';
    case Skipped = 'Skipped';
    case Incomplete = 'Incomplete';
    case Retry = 'Retry';
    case Passed = 'Passed';

    // TODO - move elsewhere
    public const PRINT_ORDER = [
        self::AbnormalTermination,
        //        self::CoverageFailure,
        self::Error,
        self::Failure,
        //        self::Warning,
        //        self::Deprecation,
        self::NoTestExecuted,
        //        self::Risky,
        self::Skipped,
        self::Incomplete,
        self::Retry,
        self::Passed,
    ];

    public static function fromStatus(TestStatus $status): self
    {
        return match ($status) {
            TestStatus::Prepared,
            TestStatus::Started,
            TestStatus::LogTerminated => throw new \InvalidArgumentException('Unexpected status as outcome: ' . $status->value),
            TestStatus::Errored => self::Error,
            TestStatus::Failed => self::Failure,
            TestStatus::MarkedIncomplete => self::Incomplete,
            TestStatus::Skipped => self::Skipped,
            TestStatus::Passed => self::Passed,
            TestStatus::Unknown => self::AbnormalTermination,
        };
    }

    public function getTitle(): string
    {
        return match ($this) {
            self::AbnormalTermination => 'abnormal terminations (fatal errors, segfaults)',
            self::Error => 'errors',
            self::Failure => 'failures',
            self::NoTestExecuted => 'no tests executed',
            self::Skipped => 'skipped',
            self::Incomplete => 'incomplete',
            self::Retry => 'retried',
            self::Passed => 'passed',
        };
    }

    public function getSymbol(): string
    {
        return match ($this) {
            self::NoTestExecuted => throw new \InvalidArgumentException('Outcome does not expect symbol: ' . $this->value),
            self::AbnormalTermination => 'X',
            self::Error => 'E',
            self::Failure => 'F',
            self::Skipped => 'S',
            self::Incomplete => 'I',
            self::Retry => 'A',
            self::Passed => '.',
        };
    }
}
