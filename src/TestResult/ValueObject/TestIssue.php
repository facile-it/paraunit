<?php

declare(strict_types=1);

namespace Paraunit\TestResult\ValueObject;

use PHPUnit\Framework\TestStatus\TestStatus;

enum TestIssue: string implements ComparableTestStatus
{
    case CoverageFailure = 'CoverageFailure';
    case Deprecation = 'Deprecation';
    case PHPUnitDeprecation = 'PHPUnitDeprecation';
    case Risky = 'Risky';
    case Warning = 'Warning';
    case Notice = 'Notice';

    public function getTitle(): string
    {
        return match ($this) {
            self::Warning => 'warnings',
            self::Deprecation => 'deprecations',
            self::PHPUnitDeprecation => 'PHPUnit deprecations',
            self::Risky => 'risky outcome',
            self::CoverageFailure => 'coverage not fetched',
            self::Notice => 'notices',
        };
    }

    public function getSymbol(): string
    {
        return match ($this) {
            self::CoverageFailure,
            self::Warning => 'W',
            self::Deprecation,
            self::PHPUnitDeprecation=> 'D',
            self::Risky => 'R',
            self::Notice => 'N',
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
            self::CoverageFailure => throw new \LogicException('Coverage failure is not present in PHPUnit statuses'),
            self::Warning => TestStatus::warning(),
            self::Deprecation,
            self::PHPUnitDeprecation => TestStatus::deprecation(),
            self::Risky => TestStatus::risky(),
            self::Notice => TestStatus::notice(),
        };
    }
}
