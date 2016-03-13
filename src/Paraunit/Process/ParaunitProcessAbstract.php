<?php

namespace Paraunit\Process;

/**
 * Class SymfonyProcessWrapper.
 */
abstract class ParaunitProcessAbstract implements ParaunitProcessInterface, RetryAwareInterface, ProcessResultInterface
{
    /** @var int */
    protected $retryCount = 0;

    /** @var bool */
    protected $shouldBeRetried = false;

    /** @var string */
    protected $uniqueId;

    /** @var string */
    protected $filename;

    /** @var string[] */
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

    /**
     */
    public function markAsToBeRetried()
    {
        $this->shouldBeRetried = true;
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
     * @return string[]
     */
    public function getTestResults()
    {
        return $this->testResults;
    }

    /**
     * @param string[] $testResults
     */
    public function setTestResults(array $testResults)
    {
        $this->testResults = $testResults;
    }

    public function addTestResult($testResult)
    {
        $this->testResults[] = $testResult;
    }

    /**
     * @return bool
     */
    public function hasAbnormalTermination()
    {
        return $this->abnormalTermination;
    }

    public function reportAbnormalTermination()
    {
        $this->abnormalTermination = true;
    }

    /**
     * @return bool
     * @deprecated
     */
    public function isEmpty()
    {
        return (bool) count($this->filename);
    }
}
