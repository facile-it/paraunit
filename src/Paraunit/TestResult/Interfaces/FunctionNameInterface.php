<?php
declare(strict_types=1);

namespace Paraunit\TestResult\Interfaces;

/**
 * Interface FunctionNameInterface
 * @package Paraunit\TestResult\Interfaces
 */
interface FunctionNameInterface
{
    public function getFunctionName(): string;
}
