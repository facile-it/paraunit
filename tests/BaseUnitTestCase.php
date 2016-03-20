<?php

namespace Tests;

use Tests\Stub\PHPUnitJSONLogOutput\JSONLogStub;

/**
 * Class BaseUnitTestCase
 * @package Paraunit\Tests
 */
abstract class BaseUnitTestCase extends BaseTestCase
{
    /**
     * @param string $event
     * @param string $status
     * @param $testOutput
     * @return \stdClass
     * @throws \Exception
     */
    protected function getLogFromStub($event = 'test', $status = 'fail', $testOutput = null)
    {
        $jsonLogs = JSONLogStub::getCleanOutputFileContent(JSONLogStub::ONE_ERROR);
        $logs = json_decode($jsonLogs);
        foreach ($logs as $log) {
            if ($log->event == $event) {
                if ($testOutput) {
                    $log->status = $status;
                    $log->message = $testOutput;
                }

                return $log;
            }
        }

        $this->fail('Feasible log message not found for test');
    }

    /**
     * @return string
     */
    protected function getWrongCoverageStubFilePath()
    {
        $filename = __DIR__ . '/Stub/CoverageOutput/WrongCoverageStub.php';
        $this->assertTrue(file_exists($filename), 'WrongCoverageStub file missing!');

        return $filename;
    }

    protected function getLogWithTrace()
    {
        $jsonLogs = JSONLogStub::getCleanOutputFileContent(JSONLogStub::ONE_ERROR);
        $logs = json_decode($jsonLogs);
        foreach ($logs as $log) {
            if (property_exists($log, 'trace') && count($log->trace)) {
                return $log;
            }
        }

        $this->fail('Feasible log message not found for test');
    }

    protected function mockTestFormat()
    {
        return $this->prophesize('Paraunit\TestResult\TestResultFormat')->reveal();
    }

    protected function mockTestResult()
    {
        return $this->prophesize('Paraunit\TestResult\Interfaces\TestResultInterface')->reveal();
    }

    protected function mockPrintableTestResult($symbol = '.')
    {
        $format = $this->prophesize('Paraunit\TestResult\TestResultFormat');
        $format->getTestResultSymbol()->willReturn($symbol);
        $result = $this->prophesize('Paraunit\TestResult\Interfaces\PrintableTestResultInterface');
        $result->getTestResultFormat()->willReturn($format->reveal());

        return $result->reveal();
    }
}
