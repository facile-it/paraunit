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

        $this->assertTrue($parser->parseAndContinue($process));

        $this->assertCount($expectedFailureCount, $process->getFailures());
    }

    public function processProvider()
    {
        return [
            [$this->getTestWithSingleError(), 0],
            [$this->getTestWith2Errors2Failures(), 2],
            [$this->getTestWithAllGreen(), 0],
            [$this->getTestWithFatalError(), 0],
        ];
    }
}
