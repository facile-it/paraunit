<?php

declare(strict_types=1);

namespace Paraunit\TestResult;

use Paraunit\TestResult\Interfaces\PrintableTestResultInterface;

class MuteTestResult extends NullTestResult implements PrintableTestResultInterface
{
    /** @var TestResultFormat */
    private $testResultFormat;

    public function __construct()
    {
        $this->testResultFormat = new TestResultFormat('null', '');
    }

    public function setTestResultFormat(TestResultFormat $testResultFormat)
    {
        $this->testResultFormat = $testResultFormat;
    }

    public function getTestResultFormat(): TestResultFormat
    {
        return $this->testResultFormat;
    }
}
