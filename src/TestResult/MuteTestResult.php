<?php

declare(strict_types=1);

namespace Paraunit\TestResult;

use Paraunit\Parser\ValueObject\Test;
use Paraunit\TestResult\Interfaces\PrintableTestResultInterface;
use Paraunit\TestResult\Interfaces\TestNameInterface;

class MuteTestResult extends NullTestResult implements TestNameInterface, PrintableTestResultInterface
{
    private TestResultFormat $testResultFormat;

    public function __construct(public readonly Test $test)
    {
        $this->testResultFormat = new TestResultFormat('null', '');
    }

    public function getTestName(): string
    {
        return $this->test->name;
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
