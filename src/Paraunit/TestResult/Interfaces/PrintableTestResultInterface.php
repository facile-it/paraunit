<?php

namespace Paraunit\TestResult\Interfaces;

use Paraunit\TestResult\TestResultFormat;

/**
 * Interface PrintableTestResultInterface
 * @package Paraunit\TestResult\Interfaces
 */
interface PrintableTestResultInterface extends TestResultInterface
{
    /**
     * @return TestResultFormat
     */
    public function getTestResultFormat();

    /**
     * @param TestResultFormat $testResultFormat
     */
    public function setTestResultFormat(TestResultFormat $testResultFormat);
}
