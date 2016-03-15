<?php

namespace Paraunit\TestResult;

/**
 * Interface TestResultInterface
 * @package Paraunit\Output\MuteTestResult
 */
interface TestResultInterface
{
    /**
     * @param $symbol
     */
    public function setTestResultSymbol($symbol);

    /**
     * @return string
     */
    public function getTestResultSymbol();
}
