<?php

declare(strict_types=1);

namespace Paraunit\TestResult;

use Paraunit\Logs\ValueObject\TestMethod;
use Paraunit\Printer\ValueObject\TestOutcome;

class TestResultContainer
{
    /** @var array<value-of<TestOutcome>, array<string, string>> */
    private array $filenames = [];

    /** @var array<value-of<TestOutcome>, TestResultWithMessage[]> */
    private array $testResults = [];

    public function addTestResult(TestResultWithMessage $testResult): void
    {
        $this->addProcessToFilenames($testResult);
        $this->testResults[$testResult->outcome->value][] = $testResult;
    }

    private function addProcessToFilenames(TestResultWithMessage $testResult): void
    {
        $name = $testResult->test instanceof TestMethod
            ? $testResult->test->className
            : $testResult->test->name;

        // trick for unique
        $this->filenames[$testResult->outcome->value][$name] = $name;
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
}
