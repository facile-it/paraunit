<?php

namespace Paraunit\TestResult\Interfaces;

/**
 * Interface TestResultContainerBearerInterface
 * @package Paraunit\Parser\Interfaces
 */
interface TestResultContainerBearerInterface
{
    /**
     * @return TestResultBearerInterface
     */
    public function getTestResultContainer();
}
