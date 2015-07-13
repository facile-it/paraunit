<?php

namespace Paraunit\Tests\Unit;


use Paraunit\Parser\RetryParser;
use Paraunit\Tests\Stub\StubbedParaProcess;

class RetryParserTest extends \PHPUnit_Framework_TestCase
{
    public function testParseAndContinue()
    {
        $process = new StubbedParaProcess();
        $process->setIsToBeRetried(true);

        $parser = new RetryParser();

        $this->assertFalse($parser->parseAndContinue($process));
        $this->assertEquals(['R'], $process->getTestResults());
    }

    public function testParseAndContinueWithNoRetry()
    {
        $process = new StubbedParaProcess();
        $process->setIsToBeRetried(false);

        $parser = new RetryParser();

        $this->assertTrue($parser->parseAndContinue($process));
        $this->assertEquals([], $process->getTestResults());
    }
}
