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

        $this->assertCount($expectedErrorCount, $process->getErrors());
    }

    public function processProvider()
    {
        return [
            [$this->getTestWithSingleError(), 1],
            [$this->getTestWith2Errors2Failures(), 2],
            [$this->getTestWithAllGreen(), 0],
            [$this->getTestWithFatalError(), 0],
        ];
    }
}
