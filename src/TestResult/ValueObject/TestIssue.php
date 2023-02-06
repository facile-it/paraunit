<?php

declare(strict_types=1);

namespace Paraunit\TestResult\ValueObject;

use PHPUnit\Framework\TestStatus\TestStatus;

enum TestIssue: string implements ComparableTestStatus
{
    case CoverageFailure = 'CoverageFailure';
    case Deprecation = 'Deprecation';
    case Risky = 'Risky';
    case Warning = 'Warning';

    public function getTitle(): string
    {
        return match ($this) {
            self::Warning => 'warnings',
            self::Deprecation => 'deprecations',
            self::Risky => 'risky outcome',
            self::CoverageFailure => 'coverage not fetched',
        };
    }

    public function getSymbol(): string
    {
        return match ($this) {
            self::CoverageFailure,
            self::Warning => 'W',
            self::Deprecation => 'D',
            self::Risky => 'R',
        };
    }

    public function isMoreImportantThan(?ComparableTestStatus $status): bool
    {
        if ($status === null) {
            return true;
        }

        return $this->toPHPUnit()->isMoreImportantThan($status->toPHPUnit());
    }

    public function toPHPUnit(): TestStatus
    {
        return match ($this) {
            self::CoverageFailure => throw new \LogicException('Coverage failure is not present in PHPUnit statuses'),
            self::Warning => TestStatus::warning(),
            self::Deprecation => TestStatus::deprecation(),
            self::Risky => TestStatus::risky(),
        };
    }
}
