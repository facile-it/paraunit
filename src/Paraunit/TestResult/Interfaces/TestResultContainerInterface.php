<?php

namespace Paraunit\TestResult\Interfaces;

use Paraunit\Process\ProcessWithResultsInterface;

/**
 * Interface TestResultContainerInterface
 * @package Paraunit\Parser\Interfaces
 */
interface TestResultContainerInterface extends TestResultBearerInterface
{
    public function handleTestResult(ProcessWithResultsInterface $process, TestResultInterface $testResult);
    
    /**
     * @return int
     */
    public function countTestResults();
}
