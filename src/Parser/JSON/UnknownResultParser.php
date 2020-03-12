<?php

declare(strict_types=1);

namespace Paraunit\Parser\JSON;

class UnknownResultParser extends GenericParser
{
    protected function logMatches(Log $log): bool
    {
        // catch 'em all!
        return true;
    }
}
