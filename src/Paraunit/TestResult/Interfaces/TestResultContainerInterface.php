<?php

namespace Paraunit\TestResult\Interfaces;

use Paraunit\TestResult\TestResultFormat;

/**
 * Interface TestResultContainerInterface
 * @package Paraunit\Parser\Interfaces
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
