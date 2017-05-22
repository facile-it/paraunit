<?php

namespace Paraunit\Process;

use Paraunit\TestResult\Interfaces\PrintableTestResultInterface;
use Paraunit\TestResult\Interfaces\TestResultBearerInterface;

/**
 * Interface ProcessWithResultsInterface
 * @package Paraunit\Process
 */
interface ProcessWithResultsInterface extends TestResultBearerInterface
{
    public function addTestResult(PrintableTestResultInterface $testResult);

    public function hasAbnormalTermination(): bool;

    public function isToBeRetried(): bool;

    public function getFilename(): string;

    /**
     * @return string|null
     */
    public function getTestClassName();

    public function setTestClassName(string $className);

    public function getUniqueId(): string;

    public function isWaitingForTestResult(): bool;

    public function setWaitingForTestResult(bool $waitingForTestResult);
}
