<?php

declare(strict_types=1);

namespace Paraunit\Logs\ValueObject;

use Paraunit\TestResult\ValueObject\PrintableTestStatus;
use Paraunit\TestResult\ValueObject\TestIssue;
use Paraunit\TestResult\ValueObject\TestOutcome;

enum LogStatus: string
{
    case Prepared = 'Prepared';
    case Errored = 'Errored';
    case Failed = 'Failed';
    case MarkedIncomplete = 'MarkedIncomplete';
    case ConsideredRisky = 'ConsideredRisky';
    case Skipped = 'Skipped';
    case Passed = 'Passed';
    case WarningTriggered = 'WarningTriggered';
    case Started = 'Started';
    case LogTerminated = 'ParaunitLogTerminated';
    case Unknown = 'Unknown';
    case Deprecation = 'Deprecation';

    public function toTestStatus(): PrintableTestStatus
    {
        return match ($this) {
            self::Prepared,
            self::Started,
            self::LogTerminated => throw new \InvalidArgumentException('Unexpected log status as outcome: ' . $this->value),
            self::Errored => TestOutcome::Error,
            self::Failed => TestOutcome::Failure,
            self::MarkedIncomplete => TestOutcome::Incomplete,
            self::Skipped => TestOutcome::Skipped,
            self::Passed => TestOutcome::Passed,
            self::Unknown => TestOutcome::AbnormalTermination,
            self::ConsideredRisky => TestIssue::Risky,
            self::WarningTriggered => TestIssue::Warning,
            self::Deprecation => TestIssue::Deprecation,
        };
    }
}
