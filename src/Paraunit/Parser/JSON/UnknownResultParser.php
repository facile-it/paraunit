<?php
declare(strict_types=1);

namespace Paraunit\Parser\JSON;

/**
 * Class UnknownResultParser
 * @package Paraunit\Parser\JSON
 */
class UnknownResultParser extends GenericParser
{
    /**
     * @param \stdClass $log
     * @return bool
     */
    protected function logMatches(\stdClass $log): bool
    {
        // catch 'em all!
        return true;
    }
}
