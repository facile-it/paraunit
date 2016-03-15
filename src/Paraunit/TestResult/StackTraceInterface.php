<?php

namespace Paraunit\TestResult;

/**
 * Interface StackTraceInterface
 * @package Paraunit\TestResult
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
