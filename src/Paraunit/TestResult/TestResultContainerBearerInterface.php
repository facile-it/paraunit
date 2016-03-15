<?php

namespace Paraunit\TestResult;

/**
 * Interface TestResultContainerBearerInterface
 * @package Paraunit\Parser
 */
interface TestResultContainerBearerInterface
{
    /**
     * @return TestResultContainerInterface
     */
    public function getTestResultContainer();
}
