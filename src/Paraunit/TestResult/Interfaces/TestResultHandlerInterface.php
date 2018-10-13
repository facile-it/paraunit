<?php

declare(strict_types=1);

namespace Paraunit\TestResult\Interfaces;

use Paraunit\Process\AbstractParaunitProcess;

/**
 * Interface TestResultHandlerInterface
 */
interface TestResultHandlerInterface
{
    public function handleTestResult(AbstractParaunitProcess $process, TestResultInterface $testResult);

    public function addProcessToFilenames(AbstractParaunitProcess $process);
}
