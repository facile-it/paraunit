<?php

declare(strict_types=1);

namespace Paraunit\Parser\ValueObject;

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
    case Finished = 'Finished';
    case LogTerminated = 'ParaunitLogTerminated';
    case Unknown = 'Unknown';
}
