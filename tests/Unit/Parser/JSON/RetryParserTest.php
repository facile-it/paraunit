<?php

declare(strict_types=1);

namespace Tests\Unit\Parser\JSON;

use Paraunit\Parser\JSON\Log;
use Paraunit\Parser\JSON\RetryParser;
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
        $log = new Log(Log::STATUS_ERROR, 'test', $testOutput);

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
        $log = new Log(Log::STATUS_ERROR, 'test', EntityManagerClosedTestStub::OUTPUT);
        $process->increaseRetryCount();

        $this->assertEquals(1, $process->getRetryCount());

        $parser = new RetryParser($this->getResultHandlerMock(false), 0);

        $this->assertFalse($parser->processWillBeRetried($process, [$log]), 'Fake retry detected');
        $this->assertFalse($process->isToBeRetried(), 'Test marked as to be retried');
    }

    /**
     * @return string[][]
     */
    public function toBeRetriedTestsProvider(): array
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
    public function notToBeRetriedTestLogsProvider(): array
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
     * @return Log[]
     */
    private function getArrayOfLogsFromStubFile(string $filename): array
    {
        $jsonObjects = json_decode(JSONLogStub::getCleanOutputFileContent($filename), true, 3, JSON_THROW_ON_ERROR);
        $logs = [];

        foreach ($jsonObjects as $jsonObject) {
            $logs[] = new Log(
                $jsonObject['status'],
                $jsonObject['test'] ?? null,
                $jsonObject['message'] ?? null
            );
        }

        return $logs;
    }

    private function getResultHandlerMock(bool $shouldBeCalled): TestResultHandlerInterface
    {
        $resultHandler = $this->prophesize(TestResultHandlerInterface::class);
        $resultHandler->handleTestResult(Argument::cetera())
            ->shouldBeCalledTimes((int) $shouldBeCalled);

        return $resultHandler->reveal();
    }
}
