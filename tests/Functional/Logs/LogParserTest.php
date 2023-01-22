<?php

declare(strict_types=1);

namespace Tests\Functional\Logs;

use Paraunit\Lifecycle\ProcessTerminated;
use Paraunit\Logs\JSON\LogParser;
use Paraunit\Printer\ValueObject\TestOutcome;
use Paraunit\TestResult\TestResult;
use Tests\BaseFunctionalTestCase;
use Tests\Stub\PHPUnitJSONLogOutput\JSONLogStub;
use Tests\Stub\StubbedParaunitProcess;

class LogParserTest extends BaseFunctionalTestCase
{
    /**
     * @dataProvider parsableResultsProvider
     */
    public function testParse(string $stubLog, string $expectedResult): void
    {
        $process = new StubbedParaunitProcess();
        $this->createLogForProcessFromStubbedLog($process, $stubLog);

        /** @var LogParser $parser */
        $parser = $this->getService(LogParser::class);

        $parser->onProcessTerminated(new ProcessTerminated($process));

        $results = $process->getTestResults();
        $this->assertContainsOnlyInstancesOf(TestResult::class, $results);
        $textResults = '';

        foreach ($results as $singleResult) {
            $textResults .= $singleResult->outcome->getSymbol();
        }

        $this->assertEquals($expectedResult, $textResults);

        if ($process->getTestClassName()) {
            $this->assertNotNull($process->getTestClassName(), 'Empty test class name');
            $this->assertStringStartsWith('Tests\\Stub\\', $process->getTestClassName());
        }
    }

    /**
     * @return (string|bool)[][]
     */
    public static function parsableResultsProvider(): array
    {
        return [
            [JSONLogStub::TWO_ERRORS_TWO_FAILURES, 'FF..E...E'],
            [JSONLogStub::ALL_GREEN, '.........'],
            [JSONLogStub::ONE_ERROR, '.E.'],
            [JSONLogStub::ONE_INCOMPLETE, '..I.'],
            [JSONLogStub::ONE_RISKY, '..R.'],
            [JSONLogStub::ONE_SKIP, '..S.'],
            [JSONLogStub::ONE_WARNING, '...W'],
            [JSONLogStub::FATAL_ERROR, 'EEX'],
            [JSONLogStub::SEGFAULT, '...X'],
            [JSONLogStub::PARSE_ERROR, '..................................................X'],
            [JSONLogStub::UNKNOWN, 'X'],
        ];
    }

    public function testParseHandlesMissingLogsAsAbnormalTerminations(): void
    {
        /** @var LogParser $parser */
        $parser = $this->getService(LogParser::class);
        $process = new StubbedParaunitProcess();
        $process->setExitCode(139);

        $parser->onProcessTerminated(new ProcessTerminated($process));

        $results = $process->getTestResults();
        $this->assertContainsOnlyInstancesOf(TestResult::class, $results);
        $this->assertCount(1, $results);
        $this->assertEquals(TestOutcome::AbnormalTermination, $results[0]->outcome);
    }
}
