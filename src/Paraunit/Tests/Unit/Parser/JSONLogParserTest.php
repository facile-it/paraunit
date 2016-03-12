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
    /**
     * @dataProvider parsableResultsProvider
     */
    public function testParse($chainedParsersResults, $expectedResult)
    {
        $chainedParsers = $this->mockChainedParsers($chainedParsersResults);
        $process = new StubbedParaProcess();
        $logLocator = $this->prophesize('Paraunit\Parser\JSONLogFetcher');
        $logLocator->fetch($process)->willReturn(JSONLogStub::getAllGreen());
        $parser = new JSONLogParser($logLocator->reveal());
        foreach ($chainedParsers as $chainedParser) {
            $parser->addParser($chainedParser);
        }

        $parser->parse($process);

        $this->markTestIncomplete('non so cosa controllare');
    }

    public function parsableResultsProvider()
    {
        return array(
            array(array(true), true),
            array(array(false, true), true),
            array(array(false, false), false),
        );
    }

    private function mockChainedParsers(array $results)
    {
        $mocks = array();
        foreach ($results as $result) {
            $parser = $this->prophesize('Paraunit\Parser\JSONParserChainElementInterface');
            $parser->parsingFoundResult(Argument::cetera())->willReturn($result);

            $mocks[] = $parser->reveal();
        }

        return $mocks;
    }

//    /**
//     * @dataProvider parsableResultsProvider
//     */
//    public function testParseAndContinue($log, $expectedResult)
//    {
//        $process = new StubbedParaProcess();
//        $logLocator = $this->prophesize('Paraunit\Parser\JSONLogFetcher');
//        $logLocator->fetch($process)->willReturn(JSONLogStub::cleanLog($log));
//
//        $parser = new JSONLogParser($logLocator->reveal());
//
//        $this->assertFalse($parser->parse($process));
//
//        $this->assertEquals($expectedResult, $process->getTestResults());
//    }
//
//    public function parsableResultsProvider()
//    {
//        return array(
//            array(JSONLogStub::get2Errors2Failures(), str_split('FF..E...E')),
//            array(JSONLogStub::getAllGreen(), str_split('.........')),
//            array(JSONLogStub::getFatalError(), str_split('...')),
//            array(JSONLogStub::getSegFault(), str_split('...')),
//            array(JSONLogStub::getSingleError(), str_split('.E.')),
//            array(JSONLogStub::getSingleIncomplete(), str_split('..I.')),
//            array(JSONLogStub::getSingleRisky(), str_split('..R.')),
//            array(JSONLogStub::getSingleSkip(), str_split('..S.')),
//            array(JSONLogStub::getSingleWarning(), str_split('...W')),
//        );
//    }

    public function testLogNotFound()
    {
        $chainedParser = $this->prophesize('Paraunit\Parser\JSONParserChainElementInterface');
        $chainedParser->parsingFoundResult(Argument::cetera())->shouldNotBeCalled();

        $process = new StubbedParaProcess();
        $logLocator = $this->prophesize('Paraunit\Parser\JSONLogFetcher');
        $logLocator->fetch($process)->willThrow(new JSONLogNotFoundException($process));

        $parser = new JSONLogParser($logLocator->reveal());
        $parser->addParser($chainedParser->reveal());

        $parser->parse($process);

        $this->assertEmpty($process->getTestResults());
   }
}
