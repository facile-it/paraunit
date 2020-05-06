<?php

declare(strict_types=1);

namespace Tests\Functional\Runner;

use Paraunit\Bin\Paraunit;
use Paraunit\Configuration\PHPUnitConfig;
use Paraunit\Configuration\PHPUnitOption;
use Paraunit\Runner\Runner;
use Tests\BaseIntegrationTestCase;
use Tests\Stub\EntityManagerClosedTestStub;
use Tests\Stub\MissingProviderTestStub;
use Tests\Stub\PassThenRetryTestStub;
use Tests\Stub\SegFaultTestStub;
use Tests\Stub\SessionTestStub;

class RunnerTest extends BaseIntegrationTestCase
{
    public function testAllGreen(): void
    {
        $this->setTextFilter('ThreeGreenTestStub.php');
        $this->loadContainer();

        $output = $this->getConsoleOutput();

        $this->assertEquals(0, $this->executeRunner(), $output->getOutput());

        $this->assertStringNotContainsString('Coverage', $output->getOutput());
        $this->assertOutputOrder($output, [
            'PARAUNIT',
            Paraunit::getVersion(),
            '...',
            '     3',
            'Execution time',
            'Executed: 1 test classes, 3 tests',
        ]);
    }

    public function testMaxRetryEntityManagerIsClosed(): void
    {
        $this->setTextFilter('EntityManagerClosedTestStub.php');
        $this->loadContainer();

        $output = $this->getConsoleOutput();

        $this->assertNotEquals(0, $this->executeRunner());

        /** @var int $retryCount */
        $retryCount = $this->getParameter('paraunit.max_retry_count');
        $this->assertStringContainsString(str_repeat('A', $retryCount) . 'E', $output->getOutput());
        $this->assertOutputOrder($output, [
            'Errors output',
            EntityManagerClosedTestStub::class . '::testBrokenTest',
            'files with ERRORS',
            EntityManagerClosedTestStub::class,
            'files with RETRIED',
            'EntityManagerClosedTestStub',
        ]);
        $this->assertStringContainsString('Executed: 1 test classes (3 retried), 1 tests', $output->getOutput());
    }

    /**
     * @dataProvider stubFilenameProvider
     */
    public function testMaxRetryDeadlock(string $stubFilePath): void
    {
        $this->setTextFilter($stubFilePath);
        $this->loadContainer();

        $exitCode = $this->executeRunner();

        $this->assertStringContainsString(str_repeat('A', 3) . 'E', $this->getConsoleOutput()->getOutput());
        $this->assertNotEquals(0, $exitCode);
    }

    /**
     * @return string[][]
     */
    public function stubFilenameProvider(): array
    {
        return [
            ['MySQLDeadLockTestStub.php'],
            ['PostgreSQLDeadLockTestStub.php'],
            ['SQLiteDeadLockTestStub.php'],
        ];
    }

    public function testSegFault(): void
    {
        $this->setTextFilter('SegFaultTestStub.php');
        $this->loadContainer();

        $this->assertNotEquals(0, $this->executeRunner(), 'Exit code should not be 0');

        $output = $this->getConsoleOutput()->getOutput();
        $this->assertMatchesRegularExpression('/\nX\s+1\n/', $output, 'Missing X output');
        $this->assertStringContainsString(
            '1 files with ABNORMAL TERMINATIONS',
            $output,
            'Missing recap title'
        );
        $this->assertStringContainsString(
            SegFaultTestStub::class,
            $output,
            'Missing failing filename'
        );
        $this->assertStringContainsString(
            'Sebastian Bergmann',
            $output,
            'Missing general output from the PHPUnit process'
        );
    }

    public function testWarning(): void
    {
        $this->setTextFilter('MissingProviderTestStub.php');
        $this->loadContainer();

        $this->executeRunner();

        $output = $this->getConsoleOutput()->getOutput();
        $this->assertMatchesRegularExpression('/\nW\s+1\n/', $output, 'Missing W output');
        $this->assertStringContainsString(
            '1 files with WARNINGS:',
            $output,
            'Missing recap title'
        );
        $this->assertStringContainsString(
            MissingProviderTestStub::class,
            $output,
            'Missing warned filename'
        );
    }

    public function testNoTestExecutedDoesntGetMistakenAsAbnormalTermination(): void
    {
        $this->setTextFilter('ThreeGreenTestStub.php');
        $this->loadContainer();

        /** @var PHPUnitConfig $phpunitConfig */
        $phpunitConfig = $this->getService(PHPUnitConfig::class);
        $option = new PHPUnitOption('group');
        $option->setValue('emptyGroup');
        $phpunitConfig->addPhpunitOption($option);

        $this->assertEquals(0, $this->executeRunner());

        $output = $this->getConsoleOutput()->getOutput();
        $this->assertStringNotContainsString('...', $output);
        $this->assertStringNotContainsString('ABNORMAL TERMINATION', $output);
        $this->assertStringContainsString('Executed: 1 test classes, 0 tests', $output);
        $this->assertStringContainsString('1 files with NO TESTS EXECUTED', $output);
        $this->assertStringContainsString('ThreeGreenTestStub.php', $output);
    }

    public function testSessionStdout(): void
    {
        $this->setTextFilter('SessionTestStub.php');
        $this->loadContainer();

        $output = $this->getConsoleOutput();

        $this->assertNotEquals(0, $this->executeRunner());
        $this->assertStringContainsString('EEE', $output->getOutput());
        $this->assertOutputOrder($output, [
            'Errors output',
            SessionTestStub::class . '::testOne',
            'headers already sent',
            SessionTestStub::class . '::testTwo',
            'headers already sent',
            SessionTestStub::class . '::testThree',
            'headers already sent',
            'files with ERRORS',
            'SessionTestStub',
        ]);

        $this->assertStringContainsString('Executed: 1 test classes, 3 tests', $output->getOutput());
    }

    public function testSessionStderr(): void
    {
        $this->setTextFilter('SessionTestStub.php');
        $this->loadContainer();

        /** @var PHPUnitConfig $phpunitConfig */
        $phpunitConfig = $this->getService(PHPUnitConfig::class);
        $option = new PHPUnitOption('stderr');
        $option->setValue('');
        $phpunitConfig->addPhpunitOption($option);

        $output = $this->getConsoleOutput();

        $this->assertSame(0, $this->executeRunner(), $output->getOutput());

        $this->assertStringNotContainsString('Coverage', $output->getOutput());
        $this->assertOutputOrder($output, [
            'PARAUNIT',
            Paraunit::getVersion(),
            '...',
            '     3',
            'Execution time',
            'Executed: 1 test classes, 3 tests',
        ]);
    }

    public function testRegressionFatalErrorsRecognizedAsUnknownResults(): void
    {
        $this->setTextFilter('FatalErrorTestStub.php');
        $this->loadContainer();

        $this->assertNotEquals(0, $this->executeRunner(), 'Exit code should not be 0');

        $output = $this->getConsoleOutput()->getOutput();
        $this->assertMatchesRegularExpression('/\nX\s+1\n/', $output, 'Missing X output');
        $this->assertStringContainsString('1 files with ABNORMAL TERMINATIONS', $output, 'Missing fatal error recap title');
        $this->assertStringNotContainsString('UNKNOWN', $output, 'REGRESSION: fatal error mistaken for unknown result');
    }

    public function testRegressionMissingLogAsUnknownResults(): void
    {
        $this->setTextFilter('ParseErrorTestStub.php');
        $this->loadContainer();

        $this->assertNotEquals(0, $this->executeRunner(), 'Exit code should not be 0');

        $output = $this->getConsoleOutput()->getOutput();
        $this->assertMatchesRegularExpression('/\nX\s+1\n/', $output, 'Missing X output');
        $this->assertStringContainsString('UNKNOWN', $output);
        $this->assertStringContainsString(
            '1 files with ABNORMAL TERMINATIONS',
            $output,
            'Missing abnormal termination recap title'
        );
    }

    public function testRegressionFatalErrorsShouldNotLeakToOutput(): void
    {
        $this->setTextFilter('RaisingNoticeTestStub.php');
        $this->loadContainer();

        $this->assertNotEquals(0, $this->executeRunner(), 'Exit code should not be 0');
        $output = $this->getConsoleOutput()->getOutput();
        $this->assertGreaterThan(
            strpos($output, 'Execution time'),
            strpos($output, 'YOU SHOULD NOT SEE THIS'),
            'REGRESSION: garbage output during tests execution (PHP warnings, var_dumps...)'
        );
    }

    public function testRegressionTestResultsBeforeRetryShouldNotBeReported(): void
    {
        $this->setTextFilter('PassThenRetryTestStub');
        $this->loadContainer();

        $this->assertNotEquals(0, $this->executeRunner(), 'Exit code should not be 0');
        $output = $this->getConsoleOutput()->getOutput();
        $this->assertMatchesRegularExpression('/^AAA\.F\.E/m', $output);
        $this->assertStringContainsString('Executed: 1 test classes (3 retried), 4 tests', $output);
        $this->assertStringContainsString('1) ' . PassThenRetryTestStub::class . '::testFail', $output);
        $this->assertStringNotContainsString('2) ' . PassThenRetryTestStub::class . '::testFail', $output, 'Failure reported more than once');
    }

    private function executeRunner(): int
    {
        /** @var Runner $runner */
        $runner = $this->getService(Runner::class);

        return $runner->run();
    }
}
