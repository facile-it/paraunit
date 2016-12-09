<?php

namespace Paraunit\TestResult;

use Paraunit\TestResult\Interfaces\FailureMessageInterface;
use Paraunit\TestResult\Interfaces\FunctionNameInterface;
use Paraunit\TestResult\Interfaces\PrintableTestResultInterface;

/**
 * Class TestResultWithMessage
 * @package Paraunit\TestResult
 */
class TestResultWithMessage extends MuteTestResult implements
    PrintableTestResultInterface,
    FunctionNameInterface,
    FailureMessageInterface
{
    /** @var  string */
    private $functionName;

    /** @var  string */
    private $failureMessage;

    /**
     * TestResultWithMessage constructor.
     * @param string $functionName
     * @param string $failureMessage
     */
    public function __construct($functionName, $failureMessage)
    {
        parent::__construct();
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
