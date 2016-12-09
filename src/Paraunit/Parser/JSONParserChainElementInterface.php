<?php

namespace Paraunit\Parser;

use Paraunit\Process\ProcessWithResultsInterface;
use Paraunit\TestResult\Interfaces\PrintableTestResultInterface;
use Paraunit\TestResult\Interfaces\TestResultInterface;

/**
 * Interface JSONParserChainElementInterface
 * @package Paraunit\Parser
 */
interface JSONParserChainElementInterface
{
    /**
     * @param ProcessWithResultsInterface $process
     * @param \stdClass $logItem
     * @return null|TestResultInterface Returned when the chain needs to stop
     */
    public function handleLogItem(ProcessWithResultsInterface $process, \stdClass $logItem);
}
