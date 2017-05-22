<?php

namespace Paraunit\TestResult\Interfaces;

use Paraunit\TestResult\TestResultFormat;

/**
 * Interface TestResultContainerInterface
 * @package Paraunit\TestResult\Interfaces
 */
interface TestResultContainerInterface extends TestResultBearerInterface
{
    /**
     * @return string[]
     */
    public function getFileNames(): array;

    public function getTestResultFormat(): TestResultFormat;

    public function countTestResults(): int;
}
