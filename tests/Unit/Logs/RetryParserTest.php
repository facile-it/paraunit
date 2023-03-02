<?php

declare(strict_types=1);

namespace Tests\Unit\Logs;

use Paraunit\Logs\JSON\RetryParser;
use Paraunit\Logs\ValueObject\LogData;
use Paraunit\Logs\ValueObject\LogStatus;
use Paraunit\Logs\ValueObject\Test;
use Paraunit\TestResult\TestResultContainer;
use Paraunit\TestResult\ValueObject\TestOutcome;
use Paraunit\TestResult\ValueObject\TestResult;
use PHPUnit\Framework\Attributes\DataProvider;
use Prophecy\Argument;
use Tests\BaseUnitTestCase;
use Tests\Stub\EntityManagerClosedTestStub;
use Tests\Stub\MySQLDeadLockTestStub;
use Tests\Stub\MySQLLockTimeoutTestStub;
use Tests\Stub\MySQLSavePointMissingTestStub;
use Tests\Stub\PostgreSQLDeadLockTestStub;
use Tests\Stub\SQLiteDeadLockTestStub;
use Tests\Stub\StubbedParaunitProcess;

class RetryParserTest extends BaseUnitTestCase
{
    #[DataProvider('toBeRetriedTestsProvider')]
    public function testParseAndSetRetry(string $testOutput): void
    {
        $log = new LogData(LogStatus::Errored, new Test('test'), $testOutput);

        $process = new StubbedParaunitProcess();
        $parser = new RetryParser($this->getResultContainerMock(true), 3);

        $this->assertTrue($parser->processWillBeRetried($process, [$log]), 'Retry not detected');
        $this->assertTrue($process->isToBeRetried(), 'Test should be marked as to be retried!');
    }

    public function testParseAndContinueWithNoRetry(): void
    {
        $log = new LogData(LogStatus::Errored, new Test('test'), 'Some error message that does not match');
        $process = new StubbedParaunitProcess();
        $parser = new RetryParser($this->getResultContainerMock(false), 3);

        $result = $parser->processWillBeRetried($process, [$log]);

        $this->assertFalse($result, 'Fake retry detected');
        $this->assertFalse($process->isToBeRetried(), 'Test marked as to be retried');
    }

    public function testParseAndContinueWithNoRetryAfterLimit(): void
    {
        $process = new StubbedParaunitProcess();
        $log = new LogData(LogStatus::Errored, new Test('test'), EntityManagerClosedTestStub::OUTPUT);
        $process->retryCount = 1;

        $parser = new RetryParser($this->getResultContainerMock(false), 0);

        $this->assertFalse($parser->processWillBeRetried($process, [$log]), 'Fake retry detected');
        $this->assertFalse($process->isToBeRetried(), 'Test marked as to be retried');
    }

    /**
     * @return array{string}[]
     */
    public static function toBeRetriedTestsProvider(): array
    {
        return [
            [EntityManagerClosedTestStub::OUTPUT],
            [MySQLDeadLockTestStub::OUTPUT],
            [MySQLLockTimeoutTestStub::OUTPUT],
            [MySQLSavePointMissingTestStub::OUTPUT],
            [PostgreSQLDeadLockTestStub::OUTPUT],
            [SQLiteDeadLockTestStub::OUTPUT],
        ];
    }

    private function getResultContainerMock(bool $shouldBeCalled): TestResultContainer
    {
        $resultHandler = $this->prophesize(TestResultContainer::class);
        $resultHandler->addTestResult(Argument::that(function (TestResult $testResult): bool {
            $this->assertSame(TestOutcome::Retry, $testResult->status);

            return true;
        }))
            ->shouldBeCalledTimes((int) $shouldBeCalled);

        return $resultHandler->reveal();
    }
}
