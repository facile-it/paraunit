<?php

namespace Paraunit\Tests\Unit;

use Paraunit\Parser\SegmentationFaultParser;
use Paraunit\Tests\Stub\StubbedParaProcess;
use Paraunit\Tests\StubbedPHPUnitBaseTestCase;

class SegmentationFaultParserTest extends StubbedPHPUnitBaseTestCase
{
    /**
     * @dataProvider processProvider
     */
    public function testParseAndContinue(StubbedParaProcess $process, $expectedResult)
    {
        $parser = new SegmentationFaultParser();

        $this->assertEquals($expectedResult, $parser->parsingFoundResult($process));
        $this->assertEquals( ! $expectedResult, $process->hasSegmentationFaults());
    }

    public function processProvider()
    {
        return array(
            array($this->getTestWithSingleError(), true),
            array($this->getTestWith2Errors2Failures(), true),
            array($this->getTestWithAllGreen(), true),
            array($this->getTestWithAllGreen5(), true),
            array($this->getTestWithFatalError(), true),
            array($this->getTestWithSegFault(), false),
        );
    }
}
