<?php

namespace Paraunit\Process;

/**
 * Class SymfonyProcessWrapper.
 */
abstract class ParaunitProcessAbstract implements ParaunitProcessInterface, RetryAwareInterface, ProcessResultInterface
{
    /**
     * @var int
     */
    protected $retryCount = 0;

    /**
     * @var bool
     */
    protected $shouldBeRetried = false;

    /**
     * @var string
     */
    protected $uniqueId;

    /**
     * @var string
     */
    protected $filename;

    /**
     * @var string[]
     */
    protected $testResults;

    /**
     * @var string[]
     */
    protected $segmentationFaults;

    /**
     * @var string[]
     */
    protected $unknownStatus;

    /**
     * @var string[]
     */
    protected $fatalErrors;

    /**
     * @var string[]
     */
    protected $errors;

    /**
     * @var string[]
     */
    protected $failures;

    /**
     * @var string[]
     */
    protected $warnings;

    /**
     * @param string $commandLine
     */
    public function __construct($commandLine)
    {
        $this->uniqueId = md5($commandLine);

        $filename = array();
        if (preg_match('/[A-z]*\.php/', $commandLine, $filename) === 1) {
            $this->filename = $filename[0];
        }

        $this->testResults = array();
        $this->segmentationFaults = array();
        $this->unknownStatus = array();
        $this->fatalErrors = array();
        $this->errors = array();
        $this->failures = array();
        $this->warnings = array();
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
     * @return string[]
     */
    public function getFatalErrors()
    {
        return $this->fatalErrors;
    }

    /**
     * @return string[]
     */
    public function getErrors()
    {
        return $this->errors;
    }

    /**
     * @return string[]
     */
    public function getFailures()
    {
        return $this->failures;
    }

    /**
     * @return string[]
     */
    public function getWarnings()
    {
        return $this->warnings;
    }

    /**
     * @param string[] $testResults
     */
    public function setTestResults(array $testResults)
    {
        $this->testResults = $testResults;
    }

    /**
     * @param string $segmentationFault
     */
    public function addSegmentationFault($segmentationFault)
    {
        $this->segmentationFaults[] = $segmentationFault;
    }

    /**
     * @param string $fatalError
     */
    public function addFatalError($fatalError)
    {
        $this->fatalErrors[] = $fatalError;
    }

    /**
     * @param string $error
     */
    public function addError($error)
    {
        $this->errors[] = $error;
    }

    /**
     * @param string $failure
     */
    public function addFailure($failure)
    {
        $this->failures[] = $failure;
    }

    /**
     * @param string $warning
     */
    public function addWarning($warning)
    {
        $this->warnings[] = $warning;
    }

    /**
     * @return bool
     */
    public function hasSegmentationFaults()
    {
        return count($this->segmentationFaults) > 0;
    }

    /**
     * @return bool
     */
    public function hasFatalErrors()
    {
        return count($this->fatalErrors) > 0;
    }

    /**
     * @return bool
     */
    public function hasErrors()
    {
        return count($this->errors) > 0;
    }

    /**
     * @return bool
     */
    public function hasFailures()
    {
        return count($this->failures) > 0;
    }

    /**
     * @return bool
     */
    public function hasWarnings()
    {
        return count($this->warnings) > 0;
    }
}
