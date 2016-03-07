<?php

namespace Paraunit\Tests\Unit;

use Paraunit\Parser\ErrorParser;
use Paraunit\Tests\Stub\StubbedParaProcess;
use Paraunit\Tests\StubbedPHPUnitBaseTestCase;

class ErrorParserTest extends StubbedPHPUnitBaseTestCase
{
    /**
     * @dataProvider processProvider
     */
    public function testParseAndContinue(StubbedParaProcess $process, $expectedErrorCount)
    {
        $parser = new ErrorParser();

        $this->assertTrue($parser->parseAndContinue($process));

        $this->assertCount($expectedErrorCount, $parser->getOutputContainer()->getOutputBuffer());
    }

    public function processProvider()
    {
        return array(
            array($this->getTestWithSingleError(), 1),
            array($this->getTestWithParserRegression(), 2),
            array($this->getTestWith2Errors2Failures(), 2),
            array($this->getTestWithAllGreen(), 0),
            array($this->getTestWithAllGreen5(), 0),
            array($this->getTestWithFatalError(), 0),
        );
    }
}
