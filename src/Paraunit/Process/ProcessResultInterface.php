<?php

namespace Paraunit\Process;

/**
 * Interface RetryAwareInterface
 * @package Paraunit\Process
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
    public function isToBeRetried();

    /**
     * @return string
     */
    public function getFilename();
}
