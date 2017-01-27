<?php
declare(strict_types=1);

namespace Tests\Functional\Runner;

use Paraunit\Bin\Paraunit;
use Paraunit\Configuration\PHPUnitConfig;
use Paraunit\Configuration\PHPUnitOption;
use Paraunit\Runner\Runner;
use Tests\BaseIntegrationTestCase;
use Tests\Stub\UnformattedOutputStub;
use Tests\Stub\EntityManagerClosedTestStub;
use Tests\Stub\SegFaultTestStub;
use Tests\Stub\MissingProviderTestStub;

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

        /** @var Runner $runner */
        $runner = $this->container->get('paraunit.runner.runner');
        $output = $this->getConsoleOutput();

        $this->assertEquals(0, $runner->run($output), $output->getOutput());

        $this->assertNotContains('Coverage', $output->getOutput());
        $this->assertOutputOrder($output, array(
            'PARAUNIT',
            'v' . Paraunit::getVersion(),
            '...',
        ));
    }

    public function testMaxRetryEntityManagerIsClosed()
    {
        $this->setTextFilter('EntityManagerClosedTestStub.php');
        $this->loadContainer();

        /** @var Runner $runner */
        $runner = $this->container->get('paraunit.runner.runner');
        $output = $this->getConsoleOutput();

        $this->assertNotEquals(0, $runner->run($output));

        $retryCount = $this->container->getParameter('paraunit.max_retry_count');
        $this->assertContains(str_repeat('A', $retryCount) . 'E', $output->getOutput());
        $this->assertOutputOrder($output, [
            'Errors output',
            EntityManagerClosedTestStub::class . '::testBrokenTest',
            'files with ERRORS',
            EntityManagerClosedTestStub::class,
            'files with RETRIED',
            EntityManagerClosedTestStub::class,
        ]);
    }

    /**
     * @dataProvider stubFilenameProvider
     */
    public function testMaxRetryDeadlock(string $stubFilePath)
    {
        $this->setTextFilter($stubFilePath);
        $this->loadContainer();

        $runner = $this->container->get('paraunit.runner.runner');
        $output = $this->getConsoleOutput();

        $exitCode = $runner->run();

        $output = $output->fetch();
        $this->assertContains(str_repeat('A', 3) . 'E', $output);
        $this->assertNotEquals(0, $exitCode);
    }

    /**
     * @return string[]
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

        $runner = $this->container->get('paraunit.runner.runner');
        $output = $this->getConsoleOutput();

        $this->assertNotEquals(
            0,
            $runner->run($output),
            'Exit code should not be 0'
        );

        $output = $outputInterface->getOutput();
        $this->assertRegExp('/\nX\n/', $output, 'Missing X output');
        $this->assertContains(
            '1 files with ABNORMAL TERMINATIONS',
            $output->getOutput(),
            'Missing recap title'
        );
        $this->assertContains(
            SegFaultTestStub::class,
            $output->getOutput(),
            'Missing failing filename'
        );
    }

    public function testWarning()
    {
        $outputInterface = new UnformattedOutputStub();
        $this->setTextFilter('MissingProviderTestStub.php');
        $this->loadContainer();

        $runner = $this->container->get('paraunit.runner.runner');
        $output = $this->getConsoleOutput();

        $this->assertNotEquals(0, $runner->run(), 'Exit code should not be 0');

        $this->assertRegExp('/\nW\n/', $output->getOutput(), 'Missing W output');
        $this->assertContains(
            '1 files with WARNINGS:',
            $output->getOutput(),
            'Missing recap title'
        );
        $this->assertContains(
            MissingProviderTestStub::class,
            $output->getOutput(),
            'Missing warned filename'
        );
    }

    public function testNoTestExecutedDoesntGetMistakenAsAbnormalTermination()
    {
        $this->setTextFilter('ThreeGreenTestStub.php');
        $this->loadContainer();

        /** @var PHPUnitConfig $phpunitConfig */
        $phpunitConfig = $this->container->get('paraunit.configuration.phpunit_config');
        $option = new PHPUnitOption('group');
        $option->setValue('emptyGroup');
        $phpunitConfig->addPhpunitOption($option);

        /** @var Runner $runner */
        $runner = $this->container->get('paraunit.runner.runner');

        $this->assertEquals(0, $runner->run());

        $output = $outputInterface->getOutput();
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

        $runner = $this->container->get('paraunit.runner.runner');

        $this->assertNotEquals(0, $runner->run(), 'Exit code should not be 0');

        $output = $outputInterface->getOutput();
        $this->assertRegExp('/\nX\n/', $output, 'Missing X output');
        $this->assertContains('1 files with ABNORMAL TERMINATIONS', $output, 'Missing fatal error recap title');
        $this->assertNotContains('UNKNOWN', $output, 'REGRESSION: fatal error mistaken for unknown result');
    }

    public function testRegressionMissingLogAsUnknownResults()
    {
        $this->setTextFilter('ParseErrorTestStub.php');
        $this->loadContainer();

        $runner = $this->container->get('paraunit.runner.runner');

        $this->assertNotEquals(0, $runner->run(), 'Exit code should not be 0');

        $output = $outputInterface->getOutput();
        $this->assertRegExp('/\nX\n/', $output, 'Missing X output');
        $this->assertContains('UNKNOWN', $output);
        $this->assertContains(
            '1 files with ABNORMAL TERMINATIONS',
            $output->getOutput(),
            'Missing abnormal termination recap title'
        );
    }

    public function testRegressionFatalErrorsShouldNotLeakToOutput()
    {
        $this->setTextFilter('RaisingNoticeTestStub.php');
        $this->loadContainer();

        $runner = $this->container->get('paraunit.runner.runner');

        $this->assertNotEquals(0, $runner->run(), 'Exit code should not be 0');
        $output = $this->getConsoleOutput();
        $this->assertGreaterThan(
            strpos($output->getOutput(), 'Execution time'),
            strpos($output->getOutput(), 'YOU SHOULD NOT SEE THIS'),
            'REGRESSION: garbage output during tests execution (PHP warnigns, var_dumps...)'
        );
    }
}
