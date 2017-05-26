<?php
declare(strict_types=1);

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
    public function getTrace(): array;

    public function addTraceStep(TraceStep $traceStep);
}
