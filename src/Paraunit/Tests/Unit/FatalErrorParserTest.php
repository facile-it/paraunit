<?php

namespace Paraunit\Tests\Unit;

use Paraunit\Parser\FatalErrorParser;
use Paraunit\Tests\Stub\StubbedParaProcess;
use Paraunit\Tests\StubbedPHPUnitBaseTestCase;

class FatalErrorParserTest extends StubbedPHPUnitBaseTestCase
{
    /**
     * @dataProvider processProvider
     */
    public function testParseAndContinue(StubbedParaProcess $process, $expectedResult)
    {
        $parser = new FatalErrorParser();

        $this->assertEquals($expectedResult, $parser->parseAndContinue($process));

        if ($expectedResult) {
            $this->assertEmpty($process->getFatalErrors());
        } else {
            $this->assertNotEmpty($process->getFatalErrors());
        }
    }

    public function processProvider()
    {
        return array(
            array($this->getTestWithSingleError(), true),
            array($this->getTestWith2Errors2Failures(), true),
            array($this->getTestWithAllGreen(), true),
            array($this->getTestWithAllGreen5(), true),
            array($this->getTestWithFatalError(), false),
        );
    }
}
