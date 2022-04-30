<?php

declare(strict_types=1);

namespace Paraunit\TestResult;

use Paraunit\Configuration\ChunkSize;
use Paraunit\Configuration\PHPUnitConfig;
use Paraunit\Process\AbstractParaunitProcess;
use Paraunit\TestResult\Interfaces\PrintableTestResultInterface;
use Paraunit\TestResult\Interfaces\TestResultContainerInterface;
use Paraunit\TestResult\Interfaces\TestResultHandlerInterface;
use Paraunit\TestResult\Interfaces\TestResultInterface;

class TestResultContainer implements TestResultContainerInterface, TestResultHandlerInterface
{
    /** @var TestResultFormat */
    private $testResultFormat;

    /** @var PHPUnitConfig */
    private $config;

    /** @var ChunkSize */
    private $chunkSize;

    /** @var string[] */
    private $filenames;

    /** @var PrintableTestResultInterface[] */
    private $testResults;

    public function __construct(
        TestResultFormat $testResultFormat,
        PHPUnitConfig $config,
        ChunkSize $chunkSize
    ) {
        $this->testResultFormat = $testResultFormat;
        $this->config = $config;
        $this->chunkSize = $chunkSize;
        $this->filenames = [];
        $this->testResults = [];
    }

    public function handleTestResult(AbstractParaunitProcess $process, TestResultInterface $testResult): void
    {
        $this->addProcessToFilenames($process);

        if ($testResult instanceof TestResultWithAbnormalTermination) {
            $this->addProcessOutputToResult($testResult, $process);
        }

        if ($testResult instanceof PrintableTestResultInterface) {
            $testResult->setTestResultFormat($this->testResultFormat);
            $this->testResults[] = $testResult;

            $process->addTestResult($testResult);
        }
    }

    public function addProcessToFilenames(AbstractParaunitProcess $process): void
    {
        $processFilename = $process->getFilename();
        if ($this->chunkSize->isChunked()) {
            $processFilename = basename($processFilename);
        }

        // trick for unique
        $this->filenames[$process->getUniqueId()] = $process->getTestClassName() ?: $processFilename;
    }

    public function getTestResultFormat(): TestResultFormat
    {
        return $this->testResultFormat;
    }

    /**
     * @return string[]
     */
    public function getFileNames(): array
    {
        return $this->filenames;
    }

    /**
     * @return PrintableTestResultInterface[]
     */
    public function getTestResults(): array
    {
        return $this->testResults;
    }

    public function countTestResults(): int
    {
        if (! $this->testResultFormat instanceof TestResultWithSymbolFormat) {
            return 0;
        }

        return count($this->testResults);
    }

    private function addProcessOutputToResult(
        TestResultWithAbnormalTermination $result,
        AbstractParaunitProcess $process
    ): void {
        $tag = $this->testResultFormat->getTag();

        $output = $this->config->getPhpunitOption('stderr') ?
            $process->getErrorOutput()
            : $process->getOutput();

        $output = $output ?: sprintf('<%s><[NO OUTPUT FOUND]></%s>', $tag, $tag);
        $result->setTestOutput($output);
    }
}
