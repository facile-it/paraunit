<?php

declare(strict_types=1);

namespace Paraunit\TestResult\Interfaces;

/**
 * Interface StackTraceInterface
 */
interface StackTraceInterface
{
    public function getTrace(): string;
}
