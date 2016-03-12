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
    protected $segmentationFault;

    /** @var bool */
    protected $fatalError;

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

        $this->segmentationFault = false;
        $this->fatalError = false;
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

    public function reportSegmentationFault()
    {
        $this->segmentationFault = true;
    }

    public function reportFatalError()
    {
        $this->fatalError = true;
    }

    /**
     * @return bool
     */
    public function hasSegmentationFaults()
    {
        return $this->segmentationFault;
    }

    /**
     * @return bool
     */
    public function hasfatalErrors()
    {
        return $this->fatalError;
    }

    /**
     * @return bool
     */
    public function isEmpty()
    {
        return (bool) count($this->filename);
    }
}
