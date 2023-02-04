<?php

declare(strict_types=1);

namespace Paraunit\TestResult;

use Paraunit\Logs\ValueObject\TestMethod;
use Paraunit\TestResult\ValueObject\TestOutcome;
use Paraunit\TestResult\ValueObject\TestResult;

class TestOutcomeContainer
{
    /** @var array<value-of<TestOutcome>, array<string, string>> */
    private array $filenames = [];

    /** @var array<value-of<TestOutcome>, TestResultWithMessage[]> */
    private array $testResults = [];

    public function addTestResult(TestResult $testResult): void
    {
        $this->addToFilenames($testResult);

        if ($testResult instanceof TestResultWithMessage) {
            $this->testResults[$testResult->status->value][] = $testResult;
        }
    }

    private function addToFilenames(TestResult $testResult): void
    {
        $name = $testResult->test instanceof TestMethod
            ? $testResult->test->className
            : $testResult->test->name;

        // trick for unique
        $this->filenames[$testResult->status->value][$name] = $name;
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
