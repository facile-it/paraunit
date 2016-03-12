<?php

namespace Paraunit\Process;

/**
 * Interface RetryAwareInterface.
 */
interface ProcessResultInterface extends OutputAwareInterface
{
    /**
     * @return string[]
     */
    public function getTestResults();

    /**
     * @param int[] $testResults
     * @deprecated We should use just addTestResult
     */
    public function setTestResults(array $testResults);

    public function addTestResult($testResult);

    /** @return bool */
    public function hasAbnormalTermination();

    /** @return string */
    public function getAbnormalTerminatedFunction();
    /**
     * @param string $test The test function that halted the process
     */
    public function reportAbnormalTerminationInFunction($test);

    /** @return bool */
    public function isToBeRetried();

    /** @return bool */
    public function isEmpty();

    /** @return string */
    public function getFilename();
}
