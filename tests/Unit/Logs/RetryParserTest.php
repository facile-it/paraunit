<?php

declare(strict_types=1);

namespace Tests\Unit\Logs;

use Paraunit\Logs\JSON\RetryParser;
use Paraunit\Logs\ValueObject\LogData;
use Paraunit\Logs\ValueObject\LogStatus;
use Paraunit\Logs\ValueObject\Test;
use Paraunit\TestResult\Interfaces\TestResultHandlerInterface;
use Prophecy\Argument;
use Tests\BaseUnitTestCase;
use Tests\Stub\EntityManagerClosedTestStub;
use Tests\Stub\MySQLDeadLockTestStub;
use Tests\Stub\MySQLLockTimeoutTestStub;
use Tests\Stub\MySQLSavePointMissingTestStub;
use Tests\Stub\PHPUnitJSONLogOutput\JSONLogStub;
use Tests\Stub\PostgreSQLDeadLockTestStub;
use Tests\Stub\SQLiteDeadLockTestStub;
use Tests\Stub\StubbedParaunitProcess;

class RetryParserTest extends BaseUnitTestCase
{
    /**
     * @dataProvider toBeRetriedTestsProvider
     */
    public function testParseAndSetRetry(string $testOutput): void
    {
        $log = new LogData(LogStatus::Errored, new Test('test'), $testOutput);

        $process = new StubbedParaunitProcess();
        $parser = new RetryParser($this->getResultHandlerMock(true), 3);

        $this->assertTrue($parser->processWillBeRetried($process, [$log]), 'Retry not detected');
        $this->assertTrue($process->isToBeRetried(), 'Test should be marked as to be retried!');
    }

    /**
     * @dataProvider notToBeRetriedTestLogsProvider
     */
    public function testParseAndContinueWithNoRetry(string $stubFilename): void
    {
        $process = new StubbedParaunitProcess();
        $parser = new RetryParser($this->getResultHandlerMock(false), 3);

        $result = $parser->processWillBeRetried($process, $this->getArrayOfLogsFromStubFile($stubFilename));

        $this->assertFalse($result, 'Fake retry detected');
        $this->assertFalse($process->isToBeRetried(), 'Test marked as to be retried');
    }

    public function testParseAndContinueWithNoRetryAfterLimit(): void
    {
        $process = new StubbedParaunitProcess();
        $log = new LogData(LogStatus::Errored, new Test('test'), EntityManagerClosedTestStub::OUTPUT);
        $process->increaseRetryCount();

        $this->assertEquals(1, $process->getRetryCount());

        $parser = new RetryParser($this->getResultHandlerMock(false), 0);

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

    /**
     * @return string[][]
     */
    public static function notToBeRetriedTestLogsProvider(): array
    {
        return [
            [JSONLogStub::TWO_ERRORS_TWO_FAILURES],
            [JSONLogStub::ALL_GREEN],
            [JSONLogStub::FATAL_ERROR],
            [JSONLogStub::SEGFAULT],
            [JSONLogStub::ONE_ERROR],
            [JSONLogStub::ONE_INCOMPLETE],
            [JSONLogStub::ONE_RISKY],
            [JSONLogStub::ONE_SKIP],
            [JSONLogStub::ONE_WARNING],
        ];
    }

    /**
     * @return LogData[]
     */
    private function getArrayOfLogsFromStubFile(string $filename): array
    {
        return LogData::parse(JSONLogStub::getCleanOutputFileContent($filename));
    }

    private function getResultHandlerMock(bool $shouldBeCalled): TestResultHandlerInterface
    {
        $resultHandler = $this->prophesize(TestResultHandlerInterface::class);
        $resultHandler->handleTestResult(Argument::cetera())
            ->shouldBeCalledTimes((int) $shouldBeCalled);

        return $resultHandler->reveal();
    }
}
