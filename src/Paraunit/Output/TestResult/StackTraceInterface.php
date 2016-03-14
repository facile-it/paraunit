<?php

namespace Paraunit\Output\TestResult;
use Paraunit\Output\TraceStep;

/**
 * Interface StackTraceInterface
 * @package Paraunit\Output\TestResult
 */
interface StackTraceInterface
{
    /**
     * @return TraceStep[]
     */
    public function getTrace();

    /**
     * @param TraceStep $traceStep
     */
    public function addTraceStep(TraceStep $traceStep);
}
