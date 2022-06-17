<?php

namespace Paraunit\Parser;

enum TestStatus: string
{
    case Prepared = 'Prepared';
    case Errored = 'Errored';
    case Failed = 'Failed';
    case MarkedIncomplete = 'MarkedIncomplete';
    case ConsideredRisky = 'ConsideredRisky';
    case Skipped = 'Skipped';
    case Passed = 'Passed';
    case PassedWithWarning = 'PassedWithWarning';
}
