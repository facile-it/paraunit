<?php

namespace Paraunit\TestResult;

use Paraunit\Process\OutputAwareInterface;
use Paraunit\Process\ProcessWithResultsInterface;
use Paraunit\TestResult\Interfaces\PrintableTestResultInterface;
use Paraunit\TestResult\Interfaces\TestResultContainerInterface;
use Paraunit\TestResult\Interfaces\TestResultHandlerInterface;
use Paraunit\TestResult\Interfaces\TestResultInterface;

/**
 * Class TestResultContainer
 * @package Paraunit\TestResult
 */
class TestResultContainer implements TestResultContainerInterface, TestResultHandlerInterface
{
    /** @var  TestResultFormat */
    private $testResultFormat;

    /** @var  string[] */
    private $filenames;

    /** @var  PrintableTestResultInterface[] */
    private $testResults;
    
    /**
     * TestResultContainer constructor.
     * @param TestResultFormat $testResultFormat
     */
    public function __construct(TestResultFormat $testResultFormat)
    {
        $this->testResultFormat = $testResultFormat;
        $this->filenames = array();
        $this->testResults = array();
    }

    /**
     * @param ProcessWithResultsInterface $process
     * @param TestResultInterface $testResult
     */
    public function handleTestResult(ProcessWithResultsInterface $process, TestResultInterface $testResult)
    {
        $this->addProcessToFilenames($process);

        if ($testResult instanceof TestResultWithAbnormalTermination && $process instanceof OutputAwareInterface) {
            $this->addProcessOutputToResult($testResult, $process);
        }

        if ($testResult instanceof PrintableTestResultInterface) {
            $testResult->setTestResultFormat($this->testResultFormat);
            $this->testResults[] = $testResult;

            $process->addTestResult($testResult);
        }
    }

    public function addProcessToFilenames(ProcessWithResultsInterface $process)
    {
        // trick for unique
        $this->filenames[$process->getUniqueId()] = $process->getTestClassName() ?: $process->getFilename();
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

    /**
     * @return PrintableTestResultInterface[]
     */
    public function getTestResults()
    {
        return $this->testResults;
    }

    /**
     * @return int
     */
    public function countTestResults()
    {
        return count($this->testResults);
    }

    /**
     * @param TestResultWithAbnormalTermination $result
     * @param OutputAwareInterface $process
     */
    private function addProcessOutputToResult(TestResultWithAbnormalTermination $result, OutputAwareInterface $process)
    {
        $tag = $this->testResultFormat->getTag();
        $output = $process->getOutput() ?: sprintf('<%s><[NO OUTPUT FOUND]></%s>', $tag, $tag);
        $result->setTestOutput($output);
    }
}
