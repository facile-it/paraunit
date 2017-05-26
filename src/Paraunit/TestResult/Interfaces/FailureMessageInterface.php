<?php
declare(strict_types=1);

namespace Paraunit\TestResult\Interfaces;

/**
 * Interface FailureMessageInterface
 * @package Paraunit\TestResult\Interfaces
 */
interface FailureMessageInterface
{
    public function getFailureMessage(): string;
}
