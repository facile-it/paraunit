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
        $outputInterface = new UnformattedOutputStub();
        $this->setTextFilter('ThreeGreenTestStub.php');
        $this->loadContainer();

        /** @var Runner $runner */
        $runner = $this->container->get('paraunit.runner.runner');

        $this->assertEquals(0, $runner->run($outputInterface), $outputInterface->getOutput());

        $this->assertNotContains('Coverage', $outputInterface->getOutput());
        $this->assertOutputOrder($outputInterface, array(
            'PARAUNIT',
            'v' . Paraunit::getVersion(),
            '...',
        ));
    }

    public function testMaxRetryEntityManagerIsClosed()
    {
        $outputInterface = new UnformattedOutputStub();
        $this->setTextFilter('EntityManagerClosedTestStub.php');
        $this->loadContainer();

        /** @var Runner $runner */
        $runner = $this->container->get('paraunit.runner.runner');

        $this->assertNotEquals(0, $runner->run($outputInterface));

        $retryCount = $this->container->getParameter('paraunit.max_retry_count');
        $this->assertContains(str_repeat('A', $retryCount) . 'E', $outputInterface->getOutput());
        $this->assertOutputOrder($outputInterface, [
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
        $outputInterface = new UnformattedOutputStub();
        $this->setTextFilter($stubFilePath);
        $this->loadContainer();

        $runner = $this->container->get('paraunit.runner.runner');
        $exitCode = $runner->run($outputInterface);

        $this->assertContains(str_repeat('A', 3) . 'E', $outputInterface->getOutput());
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
        $outputInterface = new UnformattedOutputStub();
        $this->setTextFilter('SegFaultTestStub.php');
        $this->loadContainer();

        $runner = $this->container->get('paraunit.runner.runner');

        $this->assertNotEquals(
            0,
            $runner->run($outputInterface),
            'Exit code should not be 0'
        );

        $output = $outputInterface->getOutput();
        $this->assertRegExp('/\nX\n/', $output, 'Missing X output');
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
    }

    public function testWarning()
    {
        $outputInterface = new UnformattedOutputStub();
        $this->setTextFilter('MissingProviderTestStub.php');
        $this->loadContainer();

        $runner = $this->container->get('paraunit.runner.runner');

        $this->assertNotEquals(0, $runner->run($outputInterface), 'Exit code should not be 0');

        $output = $outputInterface->getOutput();
        $this->assertRegExp('/\nW\n/', $output, 'Missing W output');
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
        $outputInterface = new UnformattedOutputStub();
        $this->setTextFilter('ThreeGreenTestStub.php');
        $this->loadContainer();
        
        /** @var PHPUnitConfig $phpunitConfig */
        $phpunitConfig = $this->container->get('paraunit.configuration.phpunit_config');
        $option = new PHPUnitOption('group');
        $option->setValue('emptyGroup');
        $phpunitConfig->addPhpunitOption($option);

        /** @var Runner $runner */
        $runner = $this->container->get('paraunit.runner.runner');

        $this->assertEquals(0, $runner->run($outputInterface));

        $output = $outputInterface->getOutput();
        $this->assertNotContains('...', $output);
        $this->assertNotContains('ABNORMAL TERMINATION', $output);
        $this->assertContains('Executed: 1 test classes, 0 tests', $output);
        $this->assertContains('1 files with NO TESTS EXECUTED', $output);
        $this->assertContains('ThreeGreenTestStub.php', $output);
    }

    public function testRegressionFatalErrorsRecognizedAsUnknownResults()
    {
        $outputInterface = new UnformattedOutputStub();
        $this->setTextFilter('FatalErrorTestStub.php');
        $this->loadContainer();

        $runner = $this->container->get('paraunit.runner.runner');

        $this->assertNotEquals(0, $runner->run($outputInterface), 'Exit code should not be 0');

        $output = $outputInterface->getOutput();
        $this->assertRegExp('/\nX\n/', $output, 'Missing X output');
        $this->assertContains('1 files with ABNORMAL TERMINATIONS', $output, 'Missing fatal error recap title');
        $this->assertNotContains('UNKNOWN', $output, 'REGRESSION: fatal error mistaken for unknown result');
    }

    public function testRegressionMissingLogAsUnknownResults()
    {
        $outputInterface = new UnformattedOutputStub();
        $this->setTextFilter('ParseErrorTestStub.php');
        $this->loadContainer();

        $runner = $this->container->get('paraunit.runner.runner');

        $this->assertNotEquals(0, $runner->run($outputInterface), 'Exit code should not be 0');

        $output = $outputInterface->getOutput();
        $this->assertRegExp('/\nX\n/', $output, 'Missing X output');
        $this->assertContains('UNKNOWN', $output);
        $this->assertContains(
            '1 files with ABNORMAL TERMINATIONS',
            $output,
            'Missing abnormal termination recap title'
        );
    }

    public function testRegressionFatalErrorsShouldNotLeakToOutput()
    {
        $outputInterface = new UnformattedOutputStub();
        $this->setTextFilter('RaisingNoticeTestStub.php');
        $this->loadContainer();

        $runner = $this->container->get('paraunit.runner.runner');

        $this->assertNotEquals(0, $runner->run($outputInterface), 'Exit code should not be 0');
        $output = $outputInterface->getOutput();
        $this->assertGreaterThan(
            strpos($output, 'Execution time'),
            strpos($output, 'YOU SHOULD NOT SEE THIS'),
            'REGRESSION: garbage output during tests execution (PHP warnigns, var_dumps...)'
        );
    }
}
