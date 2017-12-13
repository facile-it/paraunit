<?php

declare(strict_types=1);

namespace Tests\Unit\Parser\JSON;

use Paraunit\Parser\JSON\RetryParser;
use Paraunit\TestResult\Interfaces\TestResultHandlerInterface;
use Prophecy\Argument;
use Tests\BaseUnitTestCase;
use Tests\Stub\EntityManagerClosedTestStub;
use Tests\Stub\MySQLDeadLockTestStub;
use Tests\Stub\MySQLLockTimeoutTestStub;
use Tests\Stub\PHPUnitJSONLogOutput\JSONLogStub;
use Tests\Stub\SQLiteDeadLockTestStub;
use Tests\Stub\StubbedParaunitProcess;

/**
 * Class RetryParserTest
 * @package Tests\Unit\Parser\JSON
 */
class RetryParserTest extends BaseUnitTestCase
{
    /**
     * @dataProvider toBeRetriedTestsProvider
     */
    public function testParseAndSetRetry(string $testOutput)
    {
        $log = $this->getLogFromStub('test', 'error', $testOutput);

        $process = new StubbedParaunitProcess();
        $parser = new RetryParser($this->getResultHandlerMock(true), 3);

        $this->assertTrue($parser->processWillBeRetried($process, [$log]), 'Retry not detected');
        $this->assertTrue($process->isToBeRetried(), 'Test should be marked as to be retried!');
    }

    /**
     * @dataProvider notToBeRetriedTestLogsProvider
     */
    public function testParseAndContinueWithNoRetry(string $jsonLogs)
    {
        $process = new StubbedParaunitProcess();
        $parser = new RetryParser($this->getResultHandlerMock(false), 3);

        $this->assertFalse($parser->processWillBeRetried($process, json_decode($jsonLogs)), 'Fake retry detected');
        $this->assertFalse($process->isToBeRetried(), 'Test marked as to be retried');
    }

    public function testParseAndContinueWithNoRetryAfterLimit()
    {
        $process = new StubbedParaunitProcess();
        $log = $this->getLogFromStub('test', 'error', EntityManagerClosedTestStub::OUTPUT);
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
            [SQLiteDeadLockTestStub::OUTPUT],
        ];
    }

    /**
     * @return string[][]
     */
    public function notToBeRetriedTestLogsProvider(): array
    {
        return [
            [JSONLogStub::getCleanOutputFileContent(JSONLogStub::TWO_ERRORS_TWO_FAILURES)],
            [JSONLogStub::getCleanOutputFileContent(JSONLogStub::ALL_GREEN)],
            [JSONLogStub::getCleanOutputFileContent(JSONLogStub::FATAL_ERROR)],
            [JSONLogStub::getCleanOutputFileContent(JSONLogStub::SEGFAULT)],
            [JSONLogStub::getCleanOutputFileContent(JSONLogStub::ONE_ERROR)],
            [JSONLogStub::getCleanOutputFileContent(JSONLogStub::ONE_INCOMPLETE)],
            [JSONLogStub::getCleanOutputFileContent(JSONLogStub::ONE_RISKY)],
            [JSONLogStub::getCleanOutputFileContent(JSONLogStub::ONE_SKIP)],
            [JSONLogStub::getCleanOutputFileContent(JSONLogStub::ONE_WARNING)],
        ];
    }

    private function getResultHandlerMock(bool $shouldBeCalled): TestResultHandlerInterface
    {
        $resultHandler = $this->prophesize(TestResultHandlerInterface::class);
        $resultHandler->handleTestResult(Argument::cetera())
            ->shouldBeCalledTimes((int) $shouldBeCalled);

        return $resultHandler->reveal();
    }
}
