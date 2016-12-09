<?php


namespace Paraunit\TestResult\Interfaces;

use Paraunit\Process\ProcessWithResultsInterface;

/**
 * Interface TestResultHandlerInterface
 * @package Paraunit\TestResult\Interfaces
 */
interface TestResultHandlerInterface
{
    public function handleTestResult(ProcessWithResultsInterface $process, TestResultInterface $testResult);

    public function addProcessToFilenames(ProcessWithResultsInterface $process);
}
