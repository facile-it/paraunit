<?php

declare(strict_types=1);

namespace Paraunit\TestResult\Interfaces;

interface TestNameInterface
{
    public function getTestName(): string;
}
