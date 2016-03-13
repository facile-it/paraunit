<?php

namespace Paraunit\Tests\Functional\Parser;


use Paraunit\Configuration\JSONLogFilename;
use Paraunit\Parser\JSONLogParser;
use Paraunit\Tests\BaseFunctionalTestCase;
use Paraunit\Tests\Stub\PHPUnitOutput\JSONLogs\JSONLogStub;
use Paraunit\Tests\Stub\StubbedParaProcess;

/**
 * Class JSONLogParserTest
 * @package Paraunit\Tests\Functional\Parser
 */
class JSONLogParserTest extends BaseFunctionalTestCase
{
    /**
     * @dataProvider parsableResultsProvider
     */
    public function testParse($stubLog, $expectedResult, $hasAbnormalTermination = false)
    {
        $process = new StubbedParaProcess();
        $stubLogFilename = __DIR__ . '/../../Stub/PHPUnitOutput/JSONLogs/' . $stubLog . '.json';
        $this->assertTrue(file_exists($stubLogFilename), 'Stub log file missing!');

        /** @var JSONLogFilename $filename */
        $filenameService = $this->container->get('paraunit.configuration.json_log_filename');
        $filename = $filenameService->generate($process);

        copy($stubLogFilename, $filename);

        /** @var JSONLogParser $parser */
        $parser = $this->container->get('paraunit.parser.json_log_parser');

        $parser->parse($process);

        $this->assertEquals($expectedResult, $process->getTestResults());
        if ($hasAbnormalTermination) {
            $this->assertTrue($process->hasAbnormalTermination());
            $this->markTestIncomplete('Print function that is causing the fatal');
            $output = $parser->getAbnormalTerminatedOutputContainer();
            $this->assertContains('Paraunit\Tests\Stub\ThreeGreenTestStub::testGreenOne with data set #3 (null)', $output);
        }
    }


    public function parsableResultsProvider()
    {
        return array(
            array(JSONLogStub::TWO_ERRORS_TWO_FAILURES, str_split('FF..E...E')),
            array(JSONLogStub::ALL_GREEN, str_split('.........')),
            array(JSONLogStub::ONE_ERROR, str_split('.E.')),
            array(JSONLogStub::ONE_INCOMPLETE, str_split('..I.')),
            array(JSONLogStub::ONE_RISKY, str_split('..R.')),
            array(JSONLogStub::ONE_SKIP, str_split('..S.')),
            array(JSONLogStub::ONE_WARNING, str_split('...W')),
            array(JSONLogStub::FATAL_ERROR, str_split('...X'), true),
            array(JSONLogStub::SEGFAULT, str_split('...X'), true),
        );
    }
}
