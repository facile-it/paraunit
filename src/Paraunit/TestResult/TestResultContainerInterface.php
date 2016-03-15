<?php

namespace Paraunit\TestResult;

/**
 * Class TestResultContainer
 * @package Paraunit\TestResult
 */
interface TestResultContainerInterface
{
    /**
     * @return PrintableTestResultInterface[]
     */
    public function getTestResults();
}
