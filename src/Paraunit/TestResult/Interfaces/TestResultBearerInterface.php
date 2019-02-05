<?php

declare(strict_types=1);

namespace Paraunit\TestResult\Interfaces;

interface TestResultBearerInterface
{
    /**
     * @return PrintableTestResultInterface[]
     */
    public function getTestResults(): array;
}
