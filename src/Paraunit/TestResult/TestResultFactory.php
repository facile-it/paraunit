<?php

namespace Paraunit\TestResult;

/**
 * Class TestResultFactory
 * @package Paraunit\TestResult
 */
class TestResultFactory
{
    private $testResultSymbol = '?';

    public function createFromLog(\stdClass $log)
    {
        // tODO
        if (property_exists($log, 'trace')) {
            return new FullTestResult($this->testResultContainer->get);
        }
    }

    /**
     * @param string $testResultSymbol
     */
    public function setResultSymbol($testResultSymbol)
    {
        $this->testResultSymbol = $testResultSymbol;
    }
}
