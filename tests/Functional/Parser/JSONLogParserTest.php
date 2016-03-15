<?php

namespace Tests\Functional\Parser;


use Paraunit\Configuration\JSONLogFilename;
use Paraunit\Lifecycle\ProcessEvent;
use Paraunit\Parser\JSONLogParser;
use Paraunit\TestResult\TestResultContainer;
use Paraunit\TestResult\TestResultWithMessage;
use Tests\BaseFunctionalTestCase;
use Tests\Stub\PHPUnitJSONLogOutput\JSONLogStub;
use Tests\Stub\StubbedParaunitProcess;

/**
 * Class JSONLogParserTest
 * @package Tests\Functional\Parser
 */
class JSONLogParserTest extends BaseFunctionalTestCase
{
    /**
     * @dataProvider parsableResultsProvider
     */
    public function testParse($stubLog, $expectedResult, $hasAbnormalTermination = false)
    {
        $process = new StubbedParaunitProcess();
        $stubLogFilename = __DIR__ . '/../../Stub/PHPUnitJSONLogOutput/' . $stubLog . '.json';
        $this->assertTrue(file_exists($stubLogFilename), 'Stub log file missing!');

        /** @var JSONLogFilename $filename */
        $filenameService = $this->container->get('paraunit.configuration.json_log_filename');
        $filename = $filenameService->generate($process);

        copy($stubLogFilename, $filename);

        /** @var JSONLogParser $parser */
        $parser = $this->container->get('paraunit.parser.json_log_parser');

        $parser->onProcessTerminated(new ProcessEvent($process));

        $results = $process->getTestResults();
        $this->assertEquals(count($expectedResult), count($results));
        $i = 0;
        do {
            $this->assertEquals($expectedResult[$i], $results[0]->getTestResultFormat()->getTestResultSymbol());
        } while (++$i < count($results));


        if ($hasAbnormalTermination) {
            $this->assertTrue($process->hasAbnormalTermination());

            /** @var TestResultContainer $testResultContainer */
            $testResultContainer = $this->container->get('paraunit.test_result.abnormal_terminated_container');
            $results = $testResultContainer->getTestResults(); // PHP 5.3 crap, again
            $this->assertNotEmpty($results);
            $this->assertContainsOnlyInstancesOf('Paraunit\TestResult\TestResultWithMessage', $results);
            /** @var TestResultWithMessage $result */
            $result = end($results);
            $this->assertStringStartsWith(
                'Culpable test function: Paraunit\Tests\Stub\ThreeGreenTestStub::testGreenOne with data set #3 (null)',
                $result->getFailureMessage()
            );
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

    public function testParseHandlesUnknownResults()
    {
        $this->markTestIncomplete();
    }
}
