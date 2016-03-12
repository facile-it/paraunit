<?php

namespace Paraunit\Tests\Unit\Parser;

use Paraunit\Parser\RetryParser;
use Paraunit\Tests\Stub\EntityManagerClosedTestStub;
use Paraunit\Tests\Stub\MySQLDeadLockTestStub;
use Paraunit\Tests\Stub\MySQLLockTimeoutTestStub;
use Paraunit\Tests\Stub\PHPUnitOutput\JSONLogs\JSONLogStub;
use Paraunit\Tests\Stub\SQLiteDeadLockTestStub;
use Paraunit\Tests\Stub\StubbedParaProcess;
use Paraunit\Tests\StubbedPHPUnitBaseTestCase;

/**
 * Class RetryParserTest
 * @package Paraunit\Tests\Unit
 */
class RetryParserTest extends StubbedPHPUnitBaseTestCase
{
    /**
     * @dataProvider toBeRetriedTestsProvider
     */
    public function testParseAndSetRetry($testOutput)
    {
        $log = $this->getLogWithError($testOutput);
        
        $process = new StubbedParaProcess();
        $parser = new RetryParser();

        $this->assertTrue($parser->parsingFoundResult($process, $log), 'Parsing shouldn\'t continue!');
        $this->assertTrue($process->isToBeRetried(), 'Test should be marked as to be retried!');
    }

    /**
     * @dataProvider notToBeRetriedTestLogsProvider
     */
    public function testParseAndContinueWithNoRetry($jsonLogs)
    {
        $process = new StubbedParaProcess();
        $parser = new RetryParser();

        $logs = json_decode($jsonLogs);
        foreach ($logs as $singlelog) {
            if ($singlelog->event == 'test') {
                $this->assertFalse($parser->parsingFoundResult($process, $singlelog), 'Parsing should continue!');
                $this->assertFalse($process->isToBeRetried(), 'Test shouldn\'t be retried!');
            }
        }
    }

    public function testParseAndContinueWithNoRetryAfterLimit()
    {
        $process = new StubbedParaProcess();
        $log = $this->getLogWithError(EntityManagerClosedTestStub::OUTPUT);
        $process->increaseRetryCount();

        $this->assertEquals(1, $process->getRetryCount());

        $parser = new RetryParser(0);

        $this->assertFalse($parser->parsingFoundResult($process, $log), 'Parsing should continue!');
        $this->assertFalse($process->isToBeRetried(), 'Test shouldn\'t retry no more!');
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
            array(JSONLogStub::get2Errors2Failures()),
            array(JSONLogStub::getAllGreen()),
            array(JSONLogStub::getFatalError()),
            array(JSONLogStub::getSegFault()),
            array(JSONLogStub::getSingleError()),
            array(JSONLogStub::getSingleIncomplete()),
            array(JSONLogStub::getSingleRisky()),
            array(JSONLogStub::getSingleSkip()),
            array(JSONLogStub::getSingleWarning()),
        );
    }

    /**
     * @param $testOutput
     * @return \stdClass
     */
    private function getLogWithError($testOutput)
    {
        $jsonLogs = JSONLogStub::getSingleError();
        $logs = json_decode($jsonLogs);
        foreach ($logs as $log) {
            if ($log->event == 'test' && $log->status == 'error') {
                $log->message = $testOutput;

                return $log;
            }
        }

        $this->fail('Feasible log message not found for test');
    }
}
