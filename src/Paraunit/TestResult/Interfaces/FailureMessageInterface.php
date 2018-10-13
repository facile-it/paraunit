<?php

declare(strict_types=1);

namespace Paraunit\TestResult\Interfaces;

/**
 * Interface FailureMessageInterface
 */
interface FailureMessageInterface
{
    public function getFailureMessage(): string;
}
