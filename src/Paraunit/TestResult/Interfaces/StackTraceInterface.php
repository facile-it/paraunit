<?php

declare(strict_types=1);

namespace Paraunit\TestResult\Interfaces;

/**
 * Interface StackTraceInterface
 * @package Paraunit\TestResult\Interfaces
 */
interface StackTraceInterface
{
    public function getTrace(): string;
}
