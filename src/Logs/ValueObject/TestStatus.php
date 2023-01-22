<?php

declare(strict_types=1);

namespace Paraunit\Logs\ValueObject;

enum TestStatus: string
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
}
