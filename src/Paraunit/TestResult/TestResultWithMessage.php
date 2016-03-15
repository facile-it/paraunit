<?php

namespace Paraunit\TestResult;

/**
 * Class TestResultWithMessage
 * @package Paraunit\TestResult
 */
class TestResultWithMessage extends MuteTestResult implements TestResultInterface, FunctionNameInterface, FailureMessageInterface
{
    /** @var  string */
    private $functionName;

    /** @var  string */
    private $failureMessage;

    /**
     * TestResultWithMessage constructor.
     * @param string $testResultSymbol
     * @param string $functionName
     * @param string $failureMessage
     */
    public function __construct($testResultSymbol, $functionName, $failureMessage)
    {
        parent::__construct($testResultSymbol);
        $this->functionName = $functionName;
        $this->failureMessage = $failureMessage;
    }

    /**
     * @return string
     */
    public function getFunctionName()
    {
        return $this->functionName;
    }

    /**
     * @return string
     */
    public function getFailureMessage()
    {
        return $this->failureMessage;
    }
}
