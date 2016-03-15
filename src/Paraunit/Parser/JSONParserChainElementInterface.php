<?php

namespace Paraunit\Parser;

use Paraunit\TestResult\TestResultContainerInterface;
use Paraunit\TestResult\TestResultInterface;

/**
 * Interface JSONParserChainElementInterface
 * @package Paraunit\Parser
 */
interface JSONParserChainElementInterface
{
    /**
     * @param TestResultContainerInterface $process
     * @param \stdClass $log
     * @return null|TestResultInterface A result is returned when identified (and the chain needs to stop)
     */
    public function parseLog(TestResultContainerInterface $process, \stdClass $log);
}
