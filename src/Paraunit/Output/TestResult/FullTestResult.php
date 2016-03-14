<?php

namespace Paraunit\Output\TestResult;

use Paraunit\Output\TraceStep;

/**
 * Class FullTestResult
 * @package Paraunit\Output\TestResult
 */
class FullTestResult extends TestResultWithMessage implements TestResultInterface, FunctionNameInterface, FailureMessageInterface, StackTraceInterface
{
    /** @var TraceStep[] */
    private $trace;

    /**
     * FullTestResult constructor.
     * @param string $testResultSymbol
     * @param string $functionName
     * @param string $failureMessage
     */
    public function __construct($testResultSymbol, $functionName, $failureMessage)
    {
        parent::__construct($testResultSymbol, $functionName, $failureMessage);
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
