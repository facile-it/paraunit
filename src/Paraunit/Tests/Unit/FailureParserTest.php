<?php

namespace Paraunit\Tests\Unit;

use Paraunit\Parser\FailureParser;
use Paraunit\Tests\Stub\StubbedParaProcess;
use Paraunit\Tests\StubbedPHPUnitBaseTestCase;

class FailureParserTest extends StubbedPHPUnitBaseTestCase
{
    /**
     * @dataProvider processProvider
     */
    public function testParseAndContinue(StubbedParaProcess $process, $expectedFailureCount)
    {
        $parser = new FailureParser();

        $this->assertTrue($parser->parsingFoundResult($process));

        $this->assertCount($expectedFailureCount, $parser->getOutputContainer()->getOutputBuffer());
    }

    public function processProvider()
    {
        return array(
            array($this->getTestWithSingleError(), 0),
            array($this->getTestWithParserRegression(), 2),
            array($this->getTestWith2Errors2Failures(), 2),
            array($this->getTestWithAllGreen(), 0),
            array($this->getTestWithAllGreen5(), 0),
            array($this->getTestWithFatalError(), 0),
        );
    }
}
