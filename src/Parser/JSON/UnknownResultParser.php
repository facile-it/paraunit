<?php

declare(strict_types=1);

namespace Paraunit\Parser\JSON;

use Paraunit\Parser\ValueObject\LogData;

class UnknownResultParser extends GenericParser
{
    protected function logMatches(LogData $log): bool
    {
        // catch 'em all!
        return true;
    }
}
