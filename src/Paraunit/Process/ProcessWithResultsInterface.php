<?php

namespace Paraunit\Process;

use Paraunit\TestResult\Interfaces\PrintableTestResultInterface;
use Paraunit\TestResult\Interfaces\TestResultBearerInterface;

/**
 * Interface RetryAwareInterface.
 */
interface ProcessWithResultsInterface extends TestResultBearerInterface
{
    /**
     * @param PrintableTestResultInterface $testResult
     */
    public function addTestResult(PrintableTestResultInterface $testResult);

    /** @return bool */
    public function hasAbnormalTermination();

    /** @return bool */
    public function isToBeRetried();

    /** @return string */
    public function getFilename();

    /** @return string */
    public function getTestClassName();

    /** @param string $className */
    public function setTestClassName($className);

    /** @return string */
    public function getUniqueId();

    /** @return bool */
    public function isWaitingForTestResult();

    /**
     * @param boolean $waitingForTestResult
     */
    public function setWaitingForTestResult($waitingForTestResult);
}
