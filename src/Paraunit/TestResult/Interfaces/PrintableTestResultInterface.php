<?php

namespace Paraunit\TestResult\Interfaces;

use Paraunit\TestResult\TestResultFormat;

/**
 * Interface PrintableTestResultInterface
 * @package Paraunit\TestResult\Interfaces
 */
interface PrintableTestResultInterface extends TestResultInterface
{
    public function getTestResultFormat(): TestResultFormat;

    public function setTestResultFormat(TestResultFormat $testResultFormat);
}
