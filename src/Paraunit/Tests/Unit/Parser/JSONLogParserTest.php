<?php

namespace Paraunit\Tests\Unit\Parser;

use Paraunit\Exception\JSONLogNotFoundException;
use Paraunit\Parser\JSONLogParser;
use Paraunit\Tests\Stub\PHPUnitOutput\JSONLogs\JSONLogStub;
use Paraunit\Tests\Stub\StubbedParaProcess;

/**
 * Class JSONLogParserTest
 * @package Paraunit\Tests\Unit\Parser
 */
class JSONLogParserTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider parsableResultsProvider
     */
    public function testParseAndContinue($log, $expectedResult)
    {
        $process = new StubbedParaProcess();
        $logLocator = $this->prophesize('Paraunit\Parser\JSONLogFetcher');
        $logLocator->fetch($process)->willReturn(JSONLogStub::cleanLog($log));

        $parser = new JSONLogParser($logLocator->reveal());

        $this->assertFalse($parser->parseAndContinue($process));

        $this->assertEquals($expectedResult, $process->getTestResults());
    }

    public function parsableResultsProvider()
    {
        return array(
            array(JSONLogStub::get2Errors2Failures(), str_split('FF..E...E')),
            array(JSONLogStub::getAllGreen(), str_split('.........')),
            array(JSONLogStub::getFatalError(), str_split('...')),
            array(JSONLogStub::getSegFault(), str_split('...')),
            array(JSONLogStub::getSingleError(), str_split('.E.')),
            array(JSONLogStub::getSingleIncomplete(), str_split('..I.')),
            array(JSONLogStub::getSingleRisky(), str_split('..R.')),
            array(JSONLogStub::getSingleSkip(), str_split('..S.')),
            array(JSONLogStub::getSingleWarning(), str_split('...W')),
        );
    }

    public function testLogNotFound()
    {
        $process = new StubbedParaProcess();
        $logLocator = $this->prophesize('Paraunit\Parser\JSONLogFetcher');
        $logLocator->fetch($process)->willThrow(new JSONLogNotFoundException($process));

        $parser = new JSONLogParser($logLocator->reveal());

        $this->assertTrue($parser->parseAndContinue($process));

        $this->assertEmpty($process->getTestResults());
   }
}
