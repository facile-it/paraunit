<?php

namespace Paraunit\Tests\Unit\Parser;

use Paraunit\Exception\JSONLogNotFoundException;
use Paraunit\Exception\RecoverableTestErrorException;
use Paraunit\Parser\JSONLogParser;
use Paraunit\Tests\Stub\PHPUnitOutput\JSONLogs\JSONLogStub;
use Paraunit\Tests\Stub\StubbedParaProcess;
use Prophecy\Argument;

/**
 * Class JSONLogParserTest
 * @package Paraunit\Tests\Unit\Parser
 */
class JSONLogParserTest extends \PHPUnit_Framework_TestCase
{
    public function testParseHandlesMissingLogs()
    {
        $process = new StubbedParaProcess();
        $logLocator = $this->prophesize('Paraunit\Parser\JSONLogFetcher');
        $logLocator->fetch($process)->willThrow(new JSONLogNotFoundException($process));
        $parser = new JSONLogParser($logLocator->reveal());

        $parser->parse($process);

        $this->assertTrue($process->hasAbnormalTermination());
        $this->assertEquals('Unknown function -- test log not found', $process->getAbnormalTerminatedFunction());
    }
}
