<?php

declare(strict_types=1);

namespace Paraunit\TestResult;

use Paraunit\Logs\ValueObject\TestMethod;
use Paraunit\TestResult\ValueObject\TestIssue;
use Paraunit\TestResult\ValueObject\TestOutcome;
use Paraunit\TestResult\ValueObject\TestResult;

class TestResultContainer
{
    /** @var array<value-of<TestOutcome|TestIssue>, array<string, string>> */
    private array $filenames = [];

    /** @var array<value-of<TestOutcome|TestIssue>, TestResultWithMessage[]> */
    private array $testResults = [];

    /** @var array<value-of<TestOutcome|TestIssue>, 0|positive-int> */
    private array $ignoredByBaseline = [];

    public function addIgnoredByBaseline(TestOutcome|TestIssue $status): void
    {
        $this->ignoredByBaseline[$status->value] ??= 0;
        ++$this->ignoredByBaseline[$status->value];
    }

    public function getIgnoredByBaseline(TestOutcome|TestIssue $status): int
    {
        return $this->ignoredByBaseline[$status->value] ?? 0;
    }

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
    public function getFileNames(TestOutcome|TestIssue $outcome): array
    {
        return $this->filenames[$outcome->value] ?? [];
    }

    /**
     * @return TestResultWithMessage[]
     */
    public function getTestResults(TestOutcome|TestIssue $status): array
    {
        return $this->testResults[$status->value] ?? [];
    }
}
