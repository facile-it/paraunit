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
     */
    public function setTestResults(array $testResults);

    public function reportSegmentationFault();

    public function reportFatalError();

    /**
     * @return bool
     */
    public function hasSegmentationFaults();

    /**
     * @return bool
     */
    public function hasFatalErrors();

    /**
     * @return bool
     */
    public function isToBeRetried();

    /**
     * @return string
     */
    public function getFilename();
}
