<?php

declare(strict_types=1);

namespace Paraunit\TestResult\Interfaces;

use Paraunit\TestResult\TestResultFormat;

interface TestResultContainerInterface extends TestResultBearerInterface
{
    /**
     * @return string[]
     */
    public function getFileNames(): array;

    public function getTestResultFormat(): TestResultFormat;

    public function countTestResults(): int;
}
