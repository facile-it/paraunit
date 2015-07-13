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

        $this->assertEquals($expectedResult, $parser->parseAndContinue($process));
        $this->assertEquals(!$expectedResult, $process->hasSegmentationFaults());
    }

    public function processProvider()
    {
        return [
            [$this->getTestWithSingleError(), true],
            [$this->getTestWith2Errors2Failures(), true],
            [$this->getTestWithAllGreen(), true],
            [$this->getTestWithFatalError(), true],
            [$this->getTestWithSegFault(), false],
        ];
    }
}
