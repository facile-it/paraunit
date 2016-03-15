<?php

namespace Paraunit\TestResult\Interfaces;
use Paraunit\TestResult\TraceStep;

/**
 * Interface StackTraceInterface
 * @package Paraunit\TestResult\Interfaces
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
