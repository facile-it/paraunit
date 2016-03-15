<?php

namespace Paraunit\TestResult;

/**
 * Class FullTestResult
 * @package Paraunit\TestResult
 */
class FullTestResult extends TestResultWithMessage implements PrintableTestResultInterface, FunctionNameInterface, FailureMessageInterface, StackTraceInterface
{
    /** @var TraceStep[] */
    private $trace;

    /**
     * FullTestResult constructor.
     * @param TestResultFormat $testResultFormat
     * @param string $functionName
     * @param string $failureMessage
     */
    public function __construct($testResultFormat, $functionName, $failureMessage)
    {
        parent::__construct($testResultFormat, $functionName, $failureMessage);
        $this->trace = array();
    }

    /**
     * @return TraceStep[]
     */
    public function getTrace()
    {
        return $this->trace;
    }

    /**
     * @param TraceStep $traceStep
     */
    public function addTraceStep(TraceStep $traceStep)
    {
        $this->trace[] = $traceStep;
    }
}
