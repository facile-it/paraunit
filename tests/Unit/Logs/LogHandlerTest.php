<?php

declare(strict_types=1);

namespace Tests\Unit\Logs;

use Paraunit\Logs\JSON\LogHandler;
use Paraunit\Logs\ValueObject\LogData;
use Paraunit\Logs\ValueObject\LogStatus;
use Paraunit\Logs\ValueObject\Test;
use Paraunit\TestResult\TestResultContainer;
use Paraunit\TestResult\ValueObject\TestIssue;
use Paraunit\TestResult\ValueObject\TestOutcome;
use Paraunit\TestResult\ValueObject\TestResult;
use Psr\EventDispatcher\EventDispatcherInterface;
use Tests\BaseUnitTestCase;
use Tests\Stub\StubbedParaunitProcess;

class LogHandlerTest extends BaseUnitTestCase
{
    public function testParseHandlesNoTestExecuted(): void
    {
        $process = new StubbedParaunitProcess();
        $test = new Test($process->filename);

        $logHandler = new LogHandler(
            $this->prophesize(EventDispatcherInterface::class)->reveal(),
            $this->mockTestResultContainer($test, [TestOutcome::NoTestExecuted]),
        );

        $logHandler->processLog($process, new LogData(LogStatus::Started, $test, '0'));
        $logHandler->processLog($process, new LogData(LogStatus::LogTerminated, $test, ''));
    }

    public function testRegressionDoubleOutcomeWithSecondMoreImportant(): void
    {
        $process = new StubbedParaunitProcess();
        $test = new Test($process->filename);

        $logHandler = new LogHandler(
            $this->prophesize(EventDispatcherInterface::class)->reveal(),
            $this->mockTestResultContainer($test, [TestOutcome::Passed, TestIssue::Deprecation])
        );

        $logHandler->processLog($process, new LogData(LogStatus::Started, $test, '1'));
        $logHandler->processLog($process, new LogData(LogStatus::Prepared, $test, ''));
        $logHandler->processLog($process, new LogData(LogStatus::Passed, $test, ''));
        $logHandler->processLog($process, new LogData(LogStatus::Deprecation, $test, ''));
        $logHandler->processLog($process, new LogData(LogStatus::Finished, $test, ''));
        $logHandler->processLog($process, new LogData(LogStatus::LogTerminated, $test, ''));
    }

    /**
     * @param array<TestOutcome|TestIssue> $expectedStatuses
     */
    private function mockTestResultContainer(Test $test, array $expectedStatuses): TestResultContainer
    {
        $testResultContainer = $this->prophesize(TestResultContainer::class);

        foreach ($expectedStatuses as $expectedStatus) {
            $testResultContainer->addTestResult(new EqualsToken(new TestResult($test, $expectedStatus)))
                ->shouldBeCalledOnce();
        }

        return $testResultContainer->reveal();
    }
}
