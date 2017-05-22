<?php

namespace Tests;

use Tests\Stub\PHPUnitJSONLogOutput\JSONLogStub;
use Paraunit\TestResult\TestResultFormat;

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
        $this->assertFileExists($filename, 'WrongCoverageStub file missing!');

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
        $format = $this->prophesize(TestResultFormat::class);
        $format->getTag()
            ->willReturn('tag');

        return $format->reveal();
    }

    protected function mockTestResult()
    {
        return $this->prophesize('Paraunit\TestResult\Interfaces\TestResultInterface')->reveal();
    }

    protected function mockPrintableTestResult($symbol = null)
    {
        if ($symbol === null) {
            $format = $this->prophesize('Paraunit\TestResult\TestResultFormat');
        } else {
            $format = $this->prophesize('Paraunit\TestResult\TestResultWithSymbolFormat');
            $format->getTestResultSymbol()->willReturn($symbol);
        }

        $result = $this->prophesize('Paraunit\TestResult\Interfaces\PrintableTestResultInterface');
        $result->getTestResultFormat()->willReturn($format->reveal());

        return $result->reveal();
    }

    /**
     * @param string $path
     */
    protected function removeDirectory($path)
    {
        $it = new \RecursiveDirectoryIterator($path, \RecursiveDirectoryIterator::SKIP_DOTS);
        $files = new \RecursiveIteratorIterator($it, \RecursiveIteratorIterator::CHILD_FIRST);

        foreach ($files as $file) {
            if (is_dir($file)) {
                $this->removeDirectory($file);
            } else {
                unlink($file);
            }
        }

        rmdir($path);
    }
}
