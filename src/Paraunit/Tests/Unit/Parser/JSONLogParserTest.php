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
    public function testParse($chainedParsersResults)
    {
        $chainedParsers = $this->mockChainedParsers($chainedParsersResults);
        $process = new StubbedParaProcess();
        $logLocator = $this->prophesize('Paraunit\Parser\JSONLogFetcher');
        $logLocator->fetch($process)->willReturn(JSONLogStub::getCleanOutputFileContent(JSONLogStub::ALL_GREEN));
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
            array(array(true)),
            array(array(false, true)),
            array(array(false, false)),
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
}
