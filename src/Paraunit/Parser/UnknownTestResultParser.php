<?php

namespace Paraunit\Parser;

use Paraunit\Process\ProcessWithResultsInterface;

/**
 * Class UnknownTestResultParser
 * @package Paraunit\Parser
 */
class UnknownTestResultParser implements JSONParserChainElementInterface
{
    public function handleLogItem(ProcessWithResultsInterface $process, \stdClass $logItem)
    {
        // TODO: Implement handleLogItem() method.
    }
}
