<?php

declare(strict_types=1);

namespace Tests\Functional\Logs;

use Paraunit\Bin\Paraunit;
use Paraunit\Lifecycle\ProcessTerminated;
use Paraunit\Logs\JSON\LogParser;
use Paraunit\Runner\Runner;
use Paraunit\TestResult\TestResultContainer;
use Paraunit\TestResult\ValueObject\TestIssue;
use Paraunit\TestResult\ValueObject\TestOutcome;
use Paraunit\TestResult\ValueObject\TestResult;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\BaseFunctionalTestCase;
use Tests\Stub\StubbedParaunitProcess;

class LogParserTest extends BaseFunctionalTestCase
{
    #[DataProvider('genericStubFilenameProvider')]
    public function testLogParsingAgainstStubs(string $textFilter, int $expectedExitCode, string $expectedOutput): void
    {
        $expectedCount = strlen($expectedOutput);
        $this->setTextFilter($textFilter);
        $this->loadContainer();

        $output = $this->getConsoleOutput();

        $this->assertEquals($expectedExitCode, $this->getService(Runner::class)->run(), $output->getOutput());

        $this->assertStringNotContainsString('Coverage', $output->getOutput());
        $this->assertOutputOrder($output, [
            'PARAUNIT',
            Paraunit::getVersion(),
            $expectedOutput,
            sprintf('%6d', $expectedCount) . "\n",
            'Execution time',
            'Executed:',
        ]);
    }

    /**
     * @return array{string, (0|10), string}[]
     */
    public static function genericStubFilenameProvider(): array
    {
        return [
            'ThreeGreenTestStub' => [
                'ThreeGreenTestStub.php',
                0,
                '...',
            ],
            'EntityManagerClosedTestStub' => [
                'EntityManagerClosedTestStub.php',
                10,
                'AAAE',
            ],
            'FatalErrorTestStub' => [
                'FatalErrorTestStub.php',
                10,
                'X',
            ],
            'IntentionalWarningTestStub' => [
                'IntentionalWarningTestStub.php',
                0,
                'W',
            ],
            'MySQLDeadLockTestStub' => [
                'MySQLDeadLockTestStub.php',
                10,
                'AAAE',
            ],
            'MySQLLockTimeoutTestStub' => [
                'MySQLLockTimeoutTestStub.php',
                10,
                'AAAE',
            ],
            'MySQLSavePointMissingTestStub' => [
                'MySQLSavePointMissingTestStub.php',
                10,
                'AAAE',
            ],
            'ParseErrorTestStub' => [
                'ParseErrorTestStub.php',
                10,
                'X',
            ],
            'PassThenRetryTestStub' => [
                'PassThenRetryTestStub.php',
                10,
                'AAA.F.E',
            ],
            'PostgreSQLDeadLockTestStub' => [
                'PostgreSQLDeadLockTestStub.php',
                10,
                'AAAE',
            ],
            'RaisingDeprecationTestStub' => [
                'RaisingDeprecationTestStub.php',
                0,
                'DDD',
            ],
            'RaisingNoticeTestStub' => [
                'RaisingNoticeTestStub.php',
                10,
                'FFFF',
            ],
            'SegFaultTestStub' => [
                'SegFaultTestStub.php',
                10,
                'X',
            ],
            'SessionTestStub' => [
                'SessionTestStub.php',
                0,
                'WWW',
            ],
            'SQLiteDeadLockTestStub' => [
                'SQLiteDeadLockTestStub.php',
                10,
                'AAAE',
            ],
        ];
    }

    public function testParseHandlesMissingLogsAsAbnormalTerminations(): void
    {
        $parser = $this->getService(LogParser::class);
        $process = new StubbedParaunitProcess();
        $process->exitCode = 139;

        $parser->onProcessTerminated(new ProcessTerminated($process));

        $testResultContainer = $this->getService(TestResultContainer::class);
        foreach (TestIssue::cases() as $issue) {
            $this->assertEmpty($testResultContainer->getTestResults($issue));
        }
        foreach (TestOutcome::cases() as $outcome) {
            $results = $testResultContainer->getTestResults($outcome);

            if ($outcome !== TestOutcome::AbnormalTermination) {
                $this->assertEmpty($results);
                continue;
            }

            $this->assertContainsOnlyInstancesOf(TestResult::class, $results);
            $this->assertCount(1, $results);
            $this->assertEquals(TestOutcome::AbnormalTermination, $results[0]->status);
        }
    }
}
