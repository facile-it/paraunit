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

/**
 * Class RunnerTest
 * @package Tests\Functional\Runner
 */
class RunnerTest extends BaseIntegrationTestCase
{
    public function testAllGreen()
    {
        $this->setTextFilter('ThreeGreenTestStub.php');
        $this->loadContainer();

        $output = $this->getConsoleOutput();

        $this->assertEquals(0, $this->executeRunner(), $output->getOutput());

        $this->assertNotContains('Coverage', $output->getOutput());
        $this->assertOutputOrder($output, [
            'PARAUNIT',
            Paraunit::getVersion(),
            '...',
            '     3',
            'Execution time',
            'Executed: 1 test classes, 3 tests',
        ]);
    }

    public function testMaxRetryEntityManagerIsClosed()
    {
        $this->setTextFilter('EntityManagerClosedTestStub.php');
        $this->loadContainer();

        $output = $this->getConsoleOutput();

        $this->assertNotEquals(0, $this->executeRunner());

        $retryCount = $this->getParameter('paraunit.max_retry_count');
        $this->assertContains(str_repeat('A', $retryCount) . 'E', $output->getOutput());
        $this->assertOutputOrder($output, [
            'Errors output',
            EntityManagerClosedTestStub::class . '::testBrokenTest',
            'files with ERRORS',
            EntityManagerClosedTestStub::class,
            'files with RETRIED',
            'EntityManagerClosedTestStub',
        ]);
        $this->assertContains('Executed: 1 test classes (3 retried), 1 tests', $output->getOutput());
    }

    /**
     * @dataProvider stubFilenameProvider
     */
    public function testMaxRetryDeadlock(string $stubFilePath)
    {
        $this->setTextFilter($stubFilePath);
        $this->loadContainer();

        $exitCode = $this->executeRunner();

        $this->assertContains(str_repeat('A', 3) . 'E', $this->getConsoleOutput()->getOutput());
        $this->assertNotEquals(0, $exitCode);
    }

    /**
     * @return string[][]
     */
    public function stubFilenameProvider(): array
    {
        return [
            ['MySQLDeadLockTestStub.php'],
            ['SQLiteDeadLockTestStub.php'],
        ];
    }

    public function testSegFault()
    {
        $this->setTextFilter('SegFaultTestStub.php');
        $this->loadContainer();

        $this->assertNotEquals(0, $this->executeRunner(), 'Exit code should not be 0');

        $output = $this->getConsoleOutput()->getOutput();
        $this->assertRegExp('/\nX\s+1\n/', $output, 'Missing X output');
        $this->assertContains(
            '1 files with ABNORMAL TERMINATIONS',
            $output,
            'Missing recap title'
        );
        $this->assertContains(
            SegFaultTestStub::class,
            $output,
            'Missing failing filename'
        );
        $this->assertContains(
            'Sebastian Bergmann',
            $output,
            'Missing general output from the PHPUnit process'
        );
    }

    public function testWarning()
    {
        $this->setTextFilter('MissingProviderTestStub.php');
        $this->loadContainer();

        $this->assertNotEquals(0, $this->executeRunner(), 'Exit code should not be 0');

        $output = $this->getConsoleOutput()->getOutput();
        $this->assertRegExp('/\nW\s+1\n/', $output, 'Missing W output');
        $this->assertContains(
            '1 files with WARNINGS:',
            $output,
            'Missing recap title'
        );
        $this->assertContains(
            MissingProviderTestStub::class,
            $output,
            'Missing warned filename'
        );
    }

    public function testNoTestExecutedDoesntGetMistakenAsAbnormalTermination()
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
        $this->assertNotContains('...', $output);
        $this->assertNotContains('ABNORMAL TERMINATION', $output);
        $this->assertContains('Executed: 1 test classes, 0 tests', $output);
        $this->assertContains('1 files with NO TESTS EXECUTED', $output);
        $this->assertContains('ThreeGreenTestStub.php', $output);
    }

    public function testRegressionFatalErrorsRecognizedAsUnknownResults()
    {
        $this->setTextFilter('FatalErrorTestStub.php');
        $this->loadContainer();

        $this->assertNotEquals(0, $this->executeRunner(), 'Exit code should not be 0');

        $output = $this->getConsoleOutput()->getOutput();
        $this->assertRegExp('/\nX\s+1\n/', $output, 'Missing X output');
        $this->assertContains('1 files with ABNORMAL TERMINATIONS', $output, 'Missing fatal error recap title');
        $this->assertNotContains('UNKNOWN', $output, 'REGRESSION: fatal error mistaken for unknown result');
    }

    public function testRegressionMissingLogAsUnknownResults()
    {
        $this->setTextFilter('ParseErrorTestStub.php');
        $this->loadContainer();

        $this->assertNotEquals(0, $this->executeRunner(), 'Exit code should not be 0');

        $output = $this->getConsoleOutput()->getOutput();
        $this->assertRegExp('/\nX\s+1\n/', $output, 'Missing X output');
        $this->assertContains('UNKNOWN', $output);
        $this->assertContains(
            '1 files with ABNORMAL TERMINATIONS',
            $output,
            'Missing abnormal termination recap title'
        );
    }

    public function testRegressionFatalErrorsShouldNotLeakToOutput()
    {
        $this->setTextFilter('RaisingNoticeTestStub.php');
        $this->loadContainer();

        $this->assertNotEquals(0, $this->executeRunner(), 'Exit code should not be 0');
        $output = $this->getConsoleOutput()->getOutput();
        $this->assertGreaterThan(
            strpos($output, 'Execution time'),
            strpos($output, 'YOU SHOULD NOT SEE THIS'),
            'REGRESSION: garbage output during tests execution (PHP warnigns, var_dumps...)'
        );
    }

    public function testRegressionTestResultsBeforeRetryShouldNotBeReported()
    {
        $this->setTextFilter('PassThenRetryTestStub');
        $this->loadContainer();

        $this->assertNotEquals(0, $this->executeRunner(), 'Exit code should not be 0');
        $output = $this->getConsoleOutput()->getOutput();
        $this->assertRegExp('/^AAA\.F\.E/m', $output);
        $this->assertContains('Executed: 1 test classes (3 retried), 4 tests', $output);
        $this->assertContains('1) ' . PassThenRetryTestStub::class . '::testFail', $output);
        $this->assertNotContains('2) ' . PassThenRetryTestStub::class . '::testFail', $output, 'Failure reported more than once');
    }

    private function executeRunner(): int
    {
        /** @var Runner $runner */
        $runner = $this->getService(Runner::class);

        return $runner->run();
    }
}
