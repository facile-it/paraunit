<?php

namespace Paraunit\Tests\Unit\Parser;

use Paraunit\Exception\JSONLogNotFoundException;
use Paraunit\Parser\JSONLogParser;
use Paraunit\Printer\OutputContainer;
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
        $process->setOutput('Test output (core dumped)');
        $logLocator = $this->prophesize('Paraunit\Parser\JSONLogFetcher');
        $logLocator->fetch($process)->willThrow(new JSONLogNotFoundException($process));

        $parser = new JSONLogParser($logLocator->reveal(), new OutputContainer('', ''));

        $parser->parse($process);

        $this->assertTrue($process->hasAbnormalTermination());
        $outputContainer = $parser->getAbnormalTerminatedOutputContainer();
        $this->assertContains($process->getFilename(), $outputContainer->getFileNames());
        $buffer = $outputContainer->getOutputBuffer(); // PHP 5.3 workaround to direct call
        $this->assertContains('Unknown function -- test log not found', $buffer[$process->getFilename()][0]);
        $this->assertContains($process->getOutput(), $buffer[$process->getFilename()][0]);
    }

    public function testParseHandlesTruncatedLogs()
    {
        $process = new StubbedParaProcess();
        $process->setOutput('Test output (core dumped)');
        $logLocator = $this->prophesize('Paraunit\Parser\JSONLogFetcher');
        $log1 = new \stdClass();
        $log1->event = 'testStart';
        $log1->test = 'testSomething';
        $logLocator->fetch($process)->willReturn(array($log1));

        $parser = new JSONLogParser($logLocator->reveal(), new OutputContainer('', ''));

        $parser->parse($process);

        $this->assertTrue($process->hasAbnormalTermination());
        $outputContainer = $parser->getAbnormalTerminatedOutputContainer();
        $this->assertContains($process->getFilename(), $outputContainer->getFileNames());
        $buffer = $outputContainer->getOutputBuffer(); // PHP 5.3 workaround to direct call
        $this->assertContains('testSomething', $buffer[$process->getFilename()][0]);
        $this->assertContains($process->getOutput(), $buffer[$process->getFilename()][0]);
    }
}
