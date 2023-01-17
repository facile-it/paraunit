<?php

declare(strict_types=1);

namespace Paraunit\TestResult;

use Paraunit\Configuration\ChunkSize;
use Paraunit\Printer\ValueObject\TestOutcome;
use Paraunit\Process\AbstractParaunitProcess;

class TestResultContainer
{
    /** @var array<value-of<TestOutcome>, array<string, string>> */
    private array $filenames = [];

    /** @var array<value-of<TestOutcome>, TestResultWithMessage[]> */
    private array $testResults = [];

    public function __construct(private readonly ChunkSize $chunkSize)
    {
    }

    public function addTestResult(AbstractParaunitProcess $process, TestResultWithMessage $testResult): void
    {
        $this->addProcessToFilenames($process, $testResult);
        $this->testResults[$testResult->outcome->value][] = $testResult;
    }

    private function addProcessToFilenames(AbstractParaunitProcess $process, TestResultWithMessage $testResult): void
    {
        // trick for unique
        $this->filenames[$testResult->outcome->value][$process->getUniqueId()] = $process->getTestClassName() ?? $this->getProcessFilename($process);
    }

    /**
     * @return string[]
     */
    public function getFileNames(TestOutcome $outcome): array
    {
        return $this->filenames[$outcome->value] ?? [];
    }

    /**
     * @return TestResultWithMessage[]
     */
    public function getTestResults(TestOutcome $outcome): array
    {
        return $this->testResults[$outcome->value] ?? [];
    }

    private function getProcessFilename(AbstractParaunitProcess $process): string
    {
        return $this->chunkSize->isChunked()
            ? basename($process->getFilename())
            : $process->getFilename();
    }
}
