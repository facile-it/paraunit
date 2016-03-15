<?php

namespace Paraunit\Process;

use Paraunit\TestResult\TestResultContainerInterface;
use Paraunit\TestResult\TestResultInterface;

/**
 * Class AbstractParaunitProcess
 * @package Paraunit\Process
 */
abstract class AbstractParaunitProcess implements ParaunitProcessInterface, RetryAwareInterface, ProcessWithResultsInterface
{
    /** @var int */
    protected $retryCount = 0;

    /** @var bool */
    protected $shouldBeRetried = false;

    /** @var string */
    protected $uniqueId;

    /** @var string */
    protected $filename;

    /** @var TestResultInterface[] */
    protected $testResults;

    /** @var bool */
    protected $abnormalTermination;

    /**
     * {@inheritdoc}
     */
    public function __construct($commandLine, $uniqueId)
    {
        $this->uniqueId = $uniqueId;

        $filename = array();
        if (preg_match('/[A-z]*\.php/', $commandLine, $filename) === 1) {
            $this->filename = $filename[0];
        }

        $this->testResults = array();
        $this->abnormalTermination = false;
    }

    /**
     * @return string
     */
    public function getUniqueId()
    {
        return $this->uniqueId;
    }

    /**
     * @return int
     */
    public function getRetryCount()
    {
        return $this->retryCount;
    }

    /**
     */
    public function increaseRetryCount()
    {
        ++$this->retryCount;
    }

    public function markAsToBeRetried()
    {
        $this->shouldBeRetried = true;
        $this->testResults = array();
    }

    /**
     * @return bool
     */
    public function isToBeRetried()
    {
        return $this->shouldBeRetried;
    }

    /**
     * @return $this
     */
    public function reset()
    {
        $this->shouldBeRetried = false;
    }

    /**
     * @return string
     */
    public function getFilename()
    {
        return $this->filename;
    }

    /**
     * @return TestResultInterface[]
     */
    public function getTestResults()
    {
        return $this->testResults;
    }

    /**
     * @param TestResultInterface $testResult
     */
    public function addTestResult(TestResultInterface $testResult)
    {
        $this->testResults[] = $testResult;
    }

    /**
     * {@inheritdoc}
     */
    public function hasAbnormalTermination()
    {
        // TODO -- cambiare
        return $this->abnormalTermination;
    }

    public function reportAbnormalTermination(TestResultInterface $testResult)
    {
        $this->addTestResult($testResult);
        $this->abnormalTermination = true;
    }
}
