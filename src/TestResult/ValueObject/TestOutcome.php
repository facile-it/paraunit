<?php

declare(strict_types=1);

namespace Paraunit\TestResult\ValueObject;

use PHPUnit\Framework\TestStatus\TestStatus;

enum TestOutcome: string implements ComparableTestStatus
{
    case AbnormalTermination = 'AbnormalTermination';
    case Error = 'Error';
    case Failure = 'Failure';
    case NoTestExecuted = 'NoTestExecuted';
    case Skipped = 'Skipped';
    case Incomplete = 'Incomplete';
    case Retry = 'Retry';
    case Passed = 'Passed';

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

    public function isMoreImportantThan(?ComparableTestStatus $status): bool
    {
        if (! $status instanceof ComparableTestStatus) {
            return true;
        }

        return $this->toPHPUnit()->isMoreImportantThan($status->toPHPUnit());
    }

    public function toPHPUnit(): TestStatus
    {
        return match ($this) {
            self::NoTestExecuted => throw new \LogicException('No test executed is not a test status'),
            self::AbnormalTermination => TestStatus::unknown(),
            self::Retry,
            self::Error => TestStatus::error(),
            self::Failure => TestStatus::failure(),
            self::Skipped => TestStatus::skipped(),
            self::Incomplete => TestStatus::incomplete(),
            self::Passed => TestStatus::success(),
        };
    }
}
