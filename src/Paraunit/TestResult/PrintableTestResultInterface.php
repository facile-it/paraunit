<?php

namespace Paraunit\TestResult;

/**
 * Interface PrintableTestResultInterface
 * @package Paraunit\Output\MuteTestResult
 */
interface PrintableTestResultInterface extends TestResultInterface
{
    /**
     * @return TestResultFormat
     */
    public function getTestResultFormat();
}
