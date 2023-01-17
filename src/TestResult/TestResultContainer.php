<?php

declare(strict_types=1);

namespace Paraunit\TestResult;

use Paraunit\Configuration\ChunkSize;
use Paraunit\Logs\ValueObject\TestStatus;
use Paraunit\Process\AbstractParaunitProcess;
use Paraunit\TestResult\Interfaces\PrintableTestResultInterface;

class TestResultContainer
{
    /** @var array<TestStatus, string[]> */
    private array $filenames = [];

    /** @var array<TestStatus, TestResultWithMessage[]> */
    private array $testResults = [];

    public function __construct(private readonly ChunkSize $chunkSize)
    {
    }

    public function handleTestResult(AbstractParaunitProcess $process, TestResultWithMessage $testResult): void
    {
        $this->addProcessToFilenames($process, $testResult);
        $this->testResults[$testResult->status->value] = $testResult;
    }

    private function addProcessToFilenames(AbstractParaunitProcess $process, TestResultWithMessage $testResult): void
    {
        // trick for unique
        $this->filenames[$testResult->status->value][$process->getUniqueId()] = $process->getTestClassName() ?? $this->getProcessFilename($process);
    }

    /**
     * @return string[]
     */
    public function getFileNames(TestStatus $status): array
    {
        return $this->filenames[$status->value];
    }

    /**
     * @return PrintableTestResultInterface[]
     */
    public function getTestResults(TestStatus $status): array
    {
        return $this->testResults[$status->value];
    }

    private function getProcessFilename(AbstractParaunitProcess $process): string
    {
        return $this->chunkSize->isChunked()
            ? basename($process->getFilename())
            : $process->getFilename();
    }
}
