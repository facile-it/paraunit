<?php

declare(strict_types=1);

namespace Tests\Unit\Logs;

use Paraunit\Logs\JSON\LogHandler;
use Paraunit\Logs\ValueObject\LogData;
use Paraunit\Logs\ValueObject\LogStatus;
use Paraunit\Logs\ValueObject\Test;
use Paraunit\TestResult\TestResultContainer;
use Paraunit\TestResult\ValueObject\TestOutcome;
use Paraunit\TestResult\ValueObject\TestResult;
use Prophecy\Argument;
use Psr\EventDispatcher\EventDispatcherInterface;
use Tests\BaseUnitTestCase;
use Tests\Stub\StubbedParaunitProcess;

class LogHandlerTest extends BaseUnitTestCase
{
    public function testParseHandlesNoTestExecuted(): void
    {
        $process = new StubbedParaunitProcess();
        $test = new Test($process->filename);

        $testResultContainer = $this->prophesize(TestResultContainer::class);
        $testResultContainer->addTestResult(Argument::that(function (TestResult $testResult) use ($test): bool {
            $this->assertEquals(TestOutcome::NoTestExecuted, $testResult->status);
            $this->assertEquals($test, $testResult->test);

            return true;
        }));

        $logHandler = new LogHandler(
            $this->prophesize(EventDispatcherInterface::class)->reveal(),
            $testResultContainer->reveal(),
        );

        $logHandler->processLog($process, new LogData(LogStatus::Started, $test, '0'));
        $logHandler->processLog($process, new LogData(LogStatus::LogTerminated, $test, ''));
    }
}
