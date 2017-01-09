<?php

namespace Paraunit\TestResult\Interfaces;

use Paraunit\TestResult\TestResultFormat;

/**
 * Interface TestResultContainerInterface
 * @package Paraunit\TestResult\Interfaces
 */
interface TestResultContainerInterface extends TestResultBearerInterface
{
    /**
     * @return string[]
     */
    public function getFileNames();

    /**
     * @return TestResultFormat
     */
    public function getTestResultFormat();

    /**
     * @return int
     */
    public function countTestResults();
}
