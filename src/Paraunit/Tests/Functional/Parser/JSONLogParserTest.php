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
    public function testParse($log, $expectedResult, $hasAbnormalTermination = false)
    {
        $process = new StubbedParaProcess();

        /** @var JSONLogFilename $filename */
        $filenameService = $this->container->get('paraunit.configuration.json_log_filename');
        $filename = $filenameService->generate($process);

        $res = fopen($filename, 'w');
        fwrite($res, $log);
        fclose($res);

        /** @var JSONLogParser $parser */
        $parser = $this->container->get('paraunit.parser.json_log_parser');

        $parser->parse($process);
        unlink($filename);

        $this->assertEquals($expectedResult, $process->getTestResults());
        if ($hasAbnormalTermination) {
            $this->assertTrue($process->hasAbnormalTermination());
        }
    }


    public function parsableResultsProvider()
    {
        return array(
            array(JSONLogStub::getCleanOutputFileContent(JSONLogStub::TWO_ERRORS_TWO_FAILURES), str_split('FF..E...E')),
            array(JSONLogStub::getCleanOutputFileContent(JSONLogStub::ALL_GREEN), str_split('.........')),
            array(JSONLogStub::getCleanOutputFileContent(JSONLogStub::ONE_ERROR), str_split('.E.')),
            array(JSONLogStub::getCleanOutputFileContent(JSONLogStub::ONE_INCOMPLETE), str_split('..I.')),
            array(JSONLogStub::getCleanOutputFileContent(JSONLogStub::ONE_RISKY), str_split('..R.')),
            array(JSONLogStub::getCleanOutputFileContent(JSONLogStub::ONE_SKIP), str_split('..S.')),
            array(JSONLogStub::getCleanOutputFileContent(JSONLogStub::ONE_WARNING), str_split('...W')),
            array(JSONLogStub::getCleanOutputFileContent(JSONLogStub::FATAL_ERROR), str_split('...X'), true),
            array(JSONLogStub::getCleanOutputFileContent(JSONLogStub::SEGFAULT), str_split('...X'), true),
        );
    }
}
