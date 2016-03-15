<?php

namespace Tests;

use Tests\Stub\PHPUnitJSONLogOutput\JSONLogStub;

/**
 * Class BaseUnitTestCase
 * @package Paraunit\Tests
 */
abstract class BaseUnitTestCase extends \PHPUnit_Framework_TestCase
{
    /**
     * @param $testOutput
     * @return \stdClass
     */
    protected function getLogWithStatus($status, $testOutput = null)
    {
        $jsonLogs = JSONLogStub::getCleanOutputFileContent(JSONLogStub::ONE_ERROR);
        $logs = json_decode($jsonLogs);
        foreach ($logs as $log) {
            if ($log->event == 'test' && $log->status == $status) {
                if ($testOutput) {
                    $log->message = $testOutput;
                }

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

    protected function mockPrintableTestResult()
    {
        return $this->prophesize('Paraunit\TestResult\Interfaces\PrintableTestResultInterface')->reveal();
    }
}
