<?php

namespace Paraunit\TestResult;

use Paraunit\TestResult\Interfaces\FailureMessageInterface;
use Paraunit\TestResult\Interfaces\FunctionNameInterface;
use Paraunit\TestResult\Interfaces\PrintableTestResultInterface;
use Paraunit\TestResult\Interfaces\StackTraceInterface;

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
     * @param string $functionName
     * @param string $failureMessage
     */
    public function __construct(string $functionName, string $failureMessage)
    {
        parent::__construct($functionName, $failureMessage);
        $this->trace = [];
    }

    /**
     * @return TraceStep[]
     */
    public function getTrace(): array
    {
        return $this->trace;
    }

    public function addTraceStep(TraceStep $traceStep)
    {
        $this->trace[] = $traceStep;
    }
}
