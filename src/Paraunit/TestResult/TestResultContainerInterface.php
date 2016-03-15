<?php

namespace Paraunit\TestResult;

/**
 * Class TestResultContainer
 * @package Paraunit\TestResult
 */
interface TestResultContainerInterface
{
    /**
     * @return TestResultInterface[]
     */
    public function getTestResults();

    /**
     * @param TestResultInterface $testResult
     */
    public function addTestResult(TestResultInterface $testResult);
}
