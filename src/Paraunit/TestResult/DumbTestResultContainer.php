<?php

namespace Paraunit\TestResult;

use Paraunit\Process\ProcessWithResultsInterface;
use Paraunit\TestResult\Interfaces\TestFilenameBearerInterface;

/**
 * Class DumbTestResultContainer
 * @package Paraunit\TestResult
 */
class DumbTestResultContainer implements TestFilenameBearerInterface
{
    /** @var  TestResultFormat */
    protected $testResultFormat;

    /** @var  string[] */
    protected $filenames;

    /**
     * DumbTestResultContainer constructor.
     * @param TestResultFormat $testResultFormat
     */
    public function __construct(TestResultFormat $testResultFormat)
    {
        $this->testResultFormat = $testResultFormat;
        $this->filenames = array();
    }

    public function addProcessToFilenames(ProcessWithResultsInterface $process)
    {
        // trick for unique
        $this->filenames[$process->getUniqueId()] = $process->getFilename();
    }

    /**
     * @return TestResultFormat
     */
    public function getTestResultFormat()
    {
        return $this->testResultFormat;
    }

    /**
     * @return string[]
     */
    public function getFileNames()
    {
        return $this->filenames;
    }
}
