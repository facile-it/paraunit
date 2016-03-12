<?php

namespace Paraunit\Tests\Unit;

use Paraunit\Parser\WarningParser;
use Paraunit\Tests\Stub\StubbedParaProcess;
use Paraunit\Tests\StubbedPHPUnitBaseTestCase;

class WarningParserTest extends StubbedPHPUnitBaseTestCase
{
    /**
     * @dataProvider processProvider
     */
    public function testParseAndContinue(StubbedParaProcess $process, $expectedWarningCount)
    {
        $parser = new WarningParser();

        $this->assertTrue($parser->parsingFoundResult($process));

        $this->assertCount($expectedWarningCount, $parser->getOutputContainer()->getOutputBuffer());
    }

    public function processProvider()
    {
        return array(
            array($this->getTestWithSingleWarning(), 1),
            array($this->getTestWithSingleError(), 0),
            array($this->getTestWithParserRegression(), 0),
            array($this->getTestWith2Errors2Failures(), 0),
            array($this->getTestWithAllGreen(), 0),
            array($this->getTestWithAllGreen5(), 0),
            array($this->getTestWithFatalError(), 0),
        );
    }
}
