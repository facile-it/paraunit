<?php

namespace Paraunit\Process;

use Paraunit\TestResult\TestResultContainerInterface;
use Paraunit\TestResult\TestResultInterface;

/**
 * Interface RetryAwareInterface.
 */
interface ProcessWithResultsInterface extends TestResultContainerInterface
{
    /**
     * @param TestResultInterface $testResult
     */
    public function addTestResult(TestResultInterface $testResult);

    /**
     * @return bool
     */
    public function hasAbnormalTermination();

    /** @return bool */
    public function isToBeRetried();

    /** @return string */
    public function getFilename();
}
