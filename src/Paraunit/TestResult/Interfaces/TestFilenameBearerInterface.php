<?php

namespace Paraunit\TestResult\Interfaces;

use Paraunit\Process\ProcessWithResultsInterface;
use Paraunit\TestResult\TestResultFormat;

/**
 * Interface TestFilenameBearerInterface
 * @package Paraunit\TestResult\Interfaces
 */
interface TestFilenameBearerInterface
{
    public function addProcessToFilenames(ProcessWithResultsInterface $process);

    /**
     * @return string[]
     */
    public function getFileNames();

    /**
     * @return TestResultFormat
     */
    public function getTestResultFormat();
}
