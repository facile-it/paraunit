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

    /**
     * @param string $unknownStatus
     */
    public function addSegmentationFault($unknownStatus);

    /**
     * @param string $fatalError
     */
    public function addFatalError($fatalError);

    /**
     * @param string $error
     */
    public function addError($error);

    /**
     * @param string $fatalError
     */
    public function addFailure($fatalError);

    /**
     * @param string $warning
     */
    public function addWarning($warning);

    /**
     * @return string[]
     */
    public function getFatalErrors();

    /**
     * @return string[]
     */
    public function getErrors();

    /**
     * @return string[]
     */
    public function getFailures();

    /**
     * @return string[]
     */
    public function getWarnings();

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
    public function hasErrors();

    /**
     * @return bool
     */
    public function hasFailures();

    /**
     * @return bool
     */
    public function hasWarnings();

    /**
     * @return bool
     */
    public function isToBeRetried();

    /**
     * @return string
     */
    public function getFilename();
}
