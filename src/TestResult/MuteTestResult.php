<?php

declare(strict_types=1);

namespace Paraunit\TestResult;

use Paraunit\TestResult\Interfaces\PrintableTestResultInterface;
use Paraunit\TestResult\Interfaces\TestNameInterface;

class MuteTestResult extends NullTestResult implements TestNameInterface, PrintableTestResultInterface
{
    /** @var string */
    private $testName;

    /** @var TestResultFormat */
    private $testResultFormat;

    public function __construct(string $testName)
    {
        $this->testName = $testName;
        $this->testResultFormat = new TestResultFormat('null', '');
    }

    public function getTestName(): string
    {
        return $this->testName;
    }

    public function setTestResultFormat(TestResultFormat $testResultFormat): void
    {
        $this->testResultFormat = $testResultFormat;
    }

    public function getTestResultFormat(): TestResultFormat
    {
        return $this->testResultFormat;
    }
}
