<?php

declare(strict_types=1);

namespace Paraunit\TestResult\ValueObject;

enum TestIssue: string
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
}
