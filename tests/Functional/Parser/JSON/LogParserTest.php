<?php

declare(strict_types=1);

namespace Tests\Functional\Parser\JSON;

use Paraunit\Lifecycle\ProcessTerminated;
use Paraunit\Parser\JSON\LogParser;
use Paraunit\TestResult\Interfaces\PrintableTestResultInterface;
use Paraunit\TestResult\TestResultWithSymbolFormat;
use Tests\BaseFunctionalTestCase;
use Tests\Stub\PHPUnitJSONLogOutput\JSONLogStub;
use Tests\Stub\StubbedParaunitProcess;

class LogParserTest extends BaseFunctionalTestCase
{
    /**
     * @dataProvider parsableResultsProvider
     */
    public function testParse(string $stubLog, string $expectedResult, bool $hasAbnormalTermination = false): void
    {
        $process = new StubbedParaunitProcess();
        $this->createLogForProcessFromStubbedLog($process, $stubLog);

        /** @var LogParser $parser */
        $parser = $this->getService(LogParser::class);

        $parser->onProcessTerminated(new ProcessTerminated($process));

        $results = $process->getTestResults();
        $this->assertContainsOnlyInstancesOf(PrintableTestResultInterface::class, $results);
        $textResults = '';
        /** @var PrintableTestResultInterface $singleResult */
        foreach ($results as $singleResult) {
            /** @var TestResultWithSymbolFormat $formatWithSymbol */
            $formatWithSymbol = $singleResult->getTestResultFormat();
            $this->assertInstanceOf(TestResultWithSymbolFormat::class, $formatWithSymbol);
            $textResults .= $formatWithSymbol->getTestResultSymbol();
        }
        $this->assertEquals($expectedResult, $textResults);

        $this->assertEquals($hasAbnormalTermination, $process->hasAbnormalTermination());

        if ($process->getTestClassName()) {
            $this->assertNotNull($process->getTestClassName(), 'Empty test class name');
            $this->assertStringStartsWith('Paraunit\Tests\Stub\\', $process->getTestClassName());
        }
    }

    /**
     * @return (string|bool)[][]
     */
    public function parsableResultsProvider(): array
    {
        return [
            [JSONLogStub::TWO_ERRORS_TWO_FAILURES, 'FF..E...E'],
            [JSONLogStub::ALL_GREEN, '.........'],
            [JSONLogStub::ONE_ERROR, '.E.'],
            [JSONLogStub::ONE_INCOMPLETE, '..I.'],
            [JSONLogStub::ONE_RISKY, '..R.'],
            [JSONLogStub::ONE_SKIP, '..S.'],
            [JSONLogStub::ONE_WARNING, '...W'],
            [JSONLogStub::FATAL_ERROR, '...X', true],
            [JSONLogStub::SEGFAULT, '...X', true],
            [JSONLogStub::PARSE_ERROR, '...................................................X', true],
            [JSONLogStub::UNKNOWN, '?', false],
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
        $this->assertContainsOnlyInstancesOf(PrintableTestResultInterface::class, $results);
        $this->assertCount(1, $results);

        /** @var TestResultWithSymbolFormat $formatWithSymbol */
        $formatWithSymbol = $results[0]->getTestResultFormat();
        $this->assertInstanceOf(TestResultWithSymbolFormat::class, $formatWithSymbol);
        $this->assertEquals('X', $formatWithSymbol->getTestResultSymbol());
        $this->assertTrue($process->hasAbnormalTermination());
    }
}
