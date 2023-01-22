<?php

declare(strict_types=1);

namespace Paraunit\Printer\ValueObject;

use Paraunit\Logs\ValueObject\TestStatus;

enum TestOutcome: string
{
    case AbnormalTermination = 'AbnormalTermination';
    case CoverageFailure = 'CoverageFailure';
    case Error = 'Error';
    case Failure = 'Failure';
    case Warning = 'Warning';
    case Deprecation = 'Deprecation';
    case NoTestExecuted = 'NoTestExecuted';
    case Risky = 'Risky';
    case Skipped = 'Skipped';
    case Incomplete = 'Incomplete';
    case Retry = 'Retry';
    case Passed = 'Passed';

    public const PRINT_ORDER = [
        self::AbnormalTermination,
        self::CoverageFailure,
        self::Error,
        self::Failure,
        self::Warning,
        self::Deprecation,
        self::NoTestExecuted,
        self::Risky,
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
            TestStatus::ConsideredRisky => self::Risky,
            TestStatus::Deprecation => self::Deprecation,
            TestStatus::Skipped => self::Skipped,
            TestStatus::Passed => self::Passed,
            TestStatus::WarningTriggered => self::Warning,
            TestStatus::Unknown => self::AbnormalTermination,
        };
    }

    public function getTitle(): string
    {
        return match ($this) {
            self::AbnormalTermination => 'abnormal terminations (fatal errors, segfaults)',
            self::Error => 'errors',
            self::Failure => 'failures',
            self::Warning => 'warnings',
            self::Deprecation => 'deprecations',
            self::NoTestExecuted => 'no tests executed',
            self::Risky => 'risky outcome',
            self::Skipped => 'skipped',
            self::Incomplete => 'incomplete',
            self::Retry => 'retried',
            self::CoverageFailure => 'coverage not fetched',
            self::Passed => 'passed',
        };
    }

    public function getSymbol(): string
    {
        return match ($this) {
            self::CoverageFailure,
            self::NoTestExecuted => throw new \InvalidArgumentException('Outcome does not expect symbol: ' . $this->value),
            self::AbnormalTermination => 'X',
            self::Error => 'E',
            self::Failure => 'F',
            self::Warning => 'W',
            self::Deprecation => 'D',
            self::Risky => 'R',
            self::Skipped => 'S',
            self::Incomplete => 'I',
            self::Retry => 'A',
            self::Passed => '.',
        };
    }
}
