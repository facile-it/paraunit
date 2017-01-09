<?php

namespace Paraunit\Parser\JSON;

use Paraunit\Process\ProcessWithResultsInterface;
use Paraunit\TestResult\Interfaces\TestResultInterface;

/**
 * Interface ParserChainElementInterface
 * @package Paraunit\Parser\JSON
 */
interface ParserChainElementInterface
{
    /**
     * @param ProcessWithResultsInterface $process
     * @param \stdClass $logItem
     * @return null|TestResultInterface Returned when the chain needs to stop
     */
    public function handleLogItem(ProcessWithResultsInterface $process, \stdClass $logItem);
}
