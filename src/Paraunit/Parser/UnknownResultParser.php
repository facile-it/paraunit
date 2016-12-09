<?php

namespace Paraunit\Parser;

/**
 * Class UnknownResultParser
 * @package Paraunit\Parser
 */
class UnknownResultParser extends GenericParser
{
    /**
     * @param \stdClass $log
     * @return bool
     */
    protected function logMatches(\stdClass $log)
    {
        // catch 'em all!
        return true;
    }
}
