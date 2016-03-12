<?php

namespace Paraunit\Tests\Unit\Parser;

use Paraunit\Parser\ErrorParser;
use Paraunit\Printer\OutputContainer;
use Paraunit\Tests\BaseUnitTestCase;
use Paraunit\Tests\Stub\PHPUnitOutput\JSONLogs\JSONLogStub;
use Paraunit\Tests\Stub\StubbedParaProcess;
use Paraunit\Tests\StubbedPHPUnitBaseTestCase;

/**
 * Class ErrorParserTest
 * @package Paraunit\Tests\Unit\Parser
 */
class ErrorParserTest extends BaseUnitTestCase
{
    /**
     * @dataProvider logProvider
     */
    public function testParseAndContinue($jsonLogs, $expectedErrorCount)
    {
        $outputContainer = new OutputContainer('tag', 'title');
        $parser = new ErrorParser($outputContainer);
        $process = new StubbedParaProcess();
        $logs = json_decode($jsonLogs);

        foreach ($logs as $singleLog) {
            if ($singleLog->event == 'test') {
                $parsingShouldContinue = $parser->parsingFoundResult($process, $singleLog);
                $this->assertSame($singleLog->status != 'error', $parsingShouldContinue);
            }
        }

        $this->assertEquals($expectedErrorCount, $parser->getOutputContainer()->countMessages());
        $this->assertCount($expectedErrorCount, $process->getTestResults());
        foreach ($process->getTestResults() as $testResult) {
            $this->assertEquals('E', $testResult);
        }
    }

    public function logProvider()
    {
        return array(
            array(JSONLogStub::getCleanOutputFileContent(JSONLogStub::TWO_ERRORS_TWO_FAILURES), 2),
            array(JSONLogStub::getCleanOutputFileContent(JSONLogStub::ALL_GREEN), 0),
            array(JSONLogStub::getCleanOutputFileContent(JSONLogStub::FATAL_ERROR), 0),
            array(JSONLogStub::getCleanOutputFileContent(JSONLogStub::SEGFAULT), 0),
            array(JSONLogStub::getCleanOutputFileContent(JSONLogStub::ONE_ERROR), 1),
            // those will be intercepted before by other parsers
//            array(JSONLogStub::getCleanOutputFileContent(JSONLogStub::ONE_INCOMPLETE), 0),
//            array(JSONLogStub::getCleanOutputFileContent(JSONLogStub::ONE_RISKY), 1),
//            array(JSONLogStub::getCleanOutputFileContent(JSONLogStub::ONE_SKIP), 1),
//            array(JSONLogStub::getCleanOutputFileContent(JSONLogStub::ONE_WARNING), 1),
        );
    }
}
