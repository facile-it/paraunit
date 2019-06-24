<?php

declare(strict_types=1);

namespace Paraunit\TestResult\Interfaces;

interface FailureMessageInterface
{
    public function getFailureMessage(): string;
}
