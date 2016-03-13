<?php

namespace Tests\Unit\Parser;

use Paraunit\Parser\RetryParser;
use Paraunit\Printer\NullOutputContainer;
use Tests\BaseUnitTestCase;
use Tests\Stub\EntityManagerClosedTestStub;
use Tests\Stub\MySQLDeadLockTestStub;
use Tests\Stub\MySQLLockTimeoutTestStub;
use Tests\Stub\PHPUnitJSONLogOutput\JSONLogStub;
use Tests\Stub\SQLiteDeadLockTestStub;
use Tests\Stub\StubbedParaProcess;

/**
 * Class RetryParserTest
 * @package Tests\Unit
 */
class RetryParserTest extends BaseUnitTestCase
{
    /**
     * @dataProvider toBeRetriedTestsProvider
     */
    public function testParseAndSetRetry($testOutput)
    {
        $log = $this->getLogWithStatus('error', $testOutput);
        
        $process = new StubbedParaProcess();
        $parser = $this->createParserToBeTested();

        $this->assertTrue($parser->parsingFoundResult($process, $log), 'Parsing shouldn\'t continue!');
        $this->assertTrue($process->isToBeRetried(), 'Test should be marked as to be retried!');
        $this->assertContains('A', $process->getTestResults(), 'Test results should include an A!');
    }

    /**
     * @dataProvider notToBeRetriedTestLogsProvider
     */
    public function testParseAndContinueWithNoRetry($jsonLogs)
    {
        $process = new StubbedParaProcess();
        $parser = $this->createParserToBeTested();

        $logs = json_decode($jsonLogs);
        foreach ($logs as $singlelog) {
            if ($singlelog->event == 'test') {
                $this->assertFalse($parser->parsingFoundResult($process, $singlelog), 'Parsing should continue!');
                $this->assertFalse($process->isToBeRetried(), 'Test shouldn\'t be retried!');
                $this->assertNotContains('A', $process->getTestResults(), 'Test results should NOT include an A!');
            }
        }
    }

    public function testParseAndContinueWithNoRetryAfterLimit()
    {
        $process = new StubbedParaProcess();
        $log = $this->getLogWithStatus('error', EntityManagerClosedTestStub::OUTPUT);
        $process->increaseRetryCount();

        $this->assertEquals(1, $process->getRetryCount());

        $parser = $this->createParserToBeTested(0);

        $this->assertFalse($parser->parsingFoundResult($process, $log), 'Parsing should continue!');
        $this->assertFalse($process->isToBeRetried(), 'Test shouldn\'t retry no more!');
        $this->assertNotContains('A', $process->getTestResults(), 'Test results should NOT include an A!');
    }

    public function toBeRetriedTestsProvider()
    {
        return array(
            array(EntityManagerClosedTestStub::OUTPUT),
            array(MySQLDeadLockTestStub::OUTPUT),
            array(MySQLLockTimeoutTestStub::OUTPUT),
            array(SQLiteDeadLockTestStub::OUTPUT),
        );
    }

    public function notToBeRetriedTestLogsProvider()
    {
        return array(
            array(JSONLogStub::getCleanOutputFileContent(JSONLogStub::TWO_ERRORS_TWO_FAILURES)),
            array(JSONLogStub::getCleanOutputFileContent(JSONLogStub::ALL_GREEN)),
            array(JSONLogStub::getCleanOutputFileContent(JSONLogStub::FATAL_ERROR)),
            array(JSONLogStub::getCleanOutputFileContent(JSONLogStub::SEGFAULT)),
            array(JSONLogStub::getCleanOutputFileContent(JSONLogStub::ONE_ERROR)),
            array(JSONLogStub::getCleanOutputFileContent(JSONLogStub::ONE_INCOMPLETE)),
            array(JSONLogStub::getCleanOutputFileContent(JSONLogStub::ONE_RISKY)),
            array(JSONLogStub::getCleanOutputFileContent(JSONLogStub::ONE_SKIP)),
            array(JSONLogStub::getCleanOutputFileContent(JSONLogStub::ONE_WARNING)),
        );
    }

    /**
     * @param int $maxRetryCount
     * @return RetryParser
     */
    private function createParserToBeTested($maxRetryCount = 3)
    {
        $container = new NullOutputContainer('', '', 'A');

        return new RetryParser($container, $maxRetryCount);
    }
}
