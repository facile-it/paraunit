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
        return [
            [$this->getTestWithSingleError(), true],
            [$this->getTestWith2Errors2Failures(), true],
            [$this->getTestWithAllGreen(), true],
            [$this->getTestWithFatalError(), false],
        ];
    }
}
