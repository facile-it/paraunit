<?php

namespace Paraunit\Parser;

use Paraunit\Process\ProcessWithResultsInterface;
use Paraunit\TestResult\PrintableTestResultInterface;
use Paraunit\TestResult\TestResultInterface;

/**
 * Interface JSONParserChainElementInterface
 * @package Paraunit\Parser
 */
interface JSONParserChainElementInterface
{
    /**
     * @param ProcessWithResultsInterface $process
     * @param \stdClass $logItem
     * @return null|TestResultInterface|PrintableTestResultInterface Returned when the chain needs to stop
     */
    public function handleLogItem(ProcessWithResultsInterface $process, \stdClass $logItem);
}
