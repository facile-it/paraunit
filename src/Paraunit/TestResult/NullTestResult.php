<?php

namespace Paraunit\TestResult;

/**
 * Class NullTestResult
 * @package Paraunit\TestResult
 */
class NullTestResult implements TestResultInterface
{
    public function setTestResultSymbol($symbol) {}

    /**
     * @return string
     */
    public function getTestResultSymbol()
    {
        return '';
    }
}
