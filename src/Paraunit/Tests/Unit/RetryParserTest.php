<?php

namespace Paraunit\Tests\Unit;

use Paraunit\Parser\RetryParser;
use Paraunit\Tests\Stub\EntityManagerClosedTestStub;
use Paraunit\Tests\Stub\MySQLDeadLockTestStub;
use Paraunit\Tests\Stub\MySQLLockTimeoutTestStub;
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
        $process = new StubbedParaProcess();
        $process->setOutput($testOutput);

        $parser = new RetryParser();

        $this->assertFalse($parser->parseAndContinue($process), 'Parsing shouldn\'t continue!');
        $this->assertTrue($process->isToBeRetried(), 'Test should be marked as to be retried!');
    }

    /**
     * @dataProvider notToBeRetriedTestsProvider
     */
    public function testParseAndContinueWithNoRetry($testOutput)
    {
        $process = new StubbedParaProcess();
        $process->setOutput($testOutput);

        $parser = new RetryParser();

        $this->assertTrue($parser->parseAndContinue($process), 'Parsing should continue!');
        $this->assertFalse($process->isToBeRetried(), 'Test shouldn\'t retry no more!');
    }

    public function testParseAndContinueWithNoRetryAfterLimit()
    {
        $process = new StubbedParaProcess();
        $process->setOutput(EntityManagerClosedTestStub::OUTPUT);
        $process->increaseRetryCount();

        $this->assertEquals(1, $process->getRetryCount());

        $parser = new RetryParser(0);

        $this->assertTrue($parser->parseAndContinue($process), 'Parsing should continue!');
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

    public function notToBeRetriedTestsProvider()
    {
        return array(
            array(file_get_contents(__DIR__ . '/../Stub/PHPUnitOutput/AllGreen.txt')),
            array(file_get_contents(__DIR__ . '/../Stub/PHPUnitOutput/AllGreen5.txt')),
            array(file_get_contents(__DIR__ . '/../Stub/PHPUnitOutput/FatalError.txt')),
            array(file_get_contents(__DIR__ . '/../Stub/PHPUnitOutput/SegFault.txt')),
            array(file_get_contents(__DIR__ . '/../Stub/PHPUnitOutput/SingleError.txt')),
            array(file_get_contents(__DIR__ . '/../Stub/PHPUnitOutput/VeryLongOutput.txt')),
            array(file_get_contents(__DIR__ . '/../Stub/PHPUnitOutput/2Errors2Failures.txt')),
            array(file_get_contents(__DIR__ . '/../Stub/PHPUnitOutput/2Errors2Failures_parser_regression.txt')),
        );
    }
}
