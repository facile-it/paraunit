<?php

namespace Tests\Functional\Runner;

use Paraunit\Configuration\PHPUnitConfigFile;
use Paraunit\Runner\Runner;
use Tests\BaseFunctionalTestCase;
use Tests\Stub\UnformattedOutputStub;
use Symfony\Component\Console\Output\BufferedOutput;

/**
 * Class RunnerTest.
 */
class RunnerTest extends BaseFunctionalTestCase
{
    public function testAllGreen()
    {
        $outputInterface = new UnformattedOutputStub();

        /** @var Runner $runner */
        $runner = $this->container->get('paraunit.runner.runner');

        $fileArray = array('tests/Stub/ThreeGreenTestStub.php');

        $this->assertEquals(0, $runner->run($fileArray, $outputInterface, new PHPUnitConfigFile('')));

        $output = $outputInterface->fetch();
        $this->assertContains('...', $output);
    }

    public function testMaxRetryEntityManagerIsClosed()
    {
        $outputInterface = new UnformattedOutputStub();

        /** @var Runner $runner */
        $runner = $this->container->get('paraunit.runner.runner');

        $fileArray = array('tests/Stub/EntityManagerClosedTestStub.php');

        $this->assertNotEquals(0, $runner->run($fileArray, $outputInterface, new PHPUnitConfigFile('')));

        $output = $outputInterface->fetch();
        $retryCount = $this->container->getParameter('paraunit.max_retry_count');
        $this->assertContains(str_repeat('A', $retryCount) . 'E', $output);
    }

    /**
     * @dataProvider stubFilePathProvider
     */
    public function testMaxRetryDeadlock($stubFilePath)
    {
        $outputInterface = new BufferedOutput();

        $runner = $this->container->get('paraunit.runner.runner');

        $fileArray = array(
            $stubFilePath,
        );

        $this->assertNotEquals(0, $runner->run($fileArray, $outputInterface, new PHPUnitConfigFile('')));

        $output = $outputInterface->fetch();
        $this->assertContains(str_repeat('A', 3) . 'E', $output);
    }

    public function stubFilePathProvider()
    {
        return array(
            array('tests/Stub/MySQLDeadLockTestStub.php'),
            array('tests/Stub/SQLiteDeadLockTestStub.php'),
        );
    }

    public function testSegFault()
    {
        if (!extension_loaded('sigsegv')) {
            $this->markTestSkipped('The segfault cannot be reproduced in this environment');
        }

        $outputInterface = new UnformattedOutputStub();

        $runner = $this->container->get('paraunit.runner.runner');

        $fileArray = array('tests/Stub/SegFaultTestStub.php');

        $this->assertNotEquals(
            0,
            $runner->run($fileArray, $outputInterface, new PHPUnitConfigFile('')),
            'Exit code should not be 0'
        );

        $output = $outputInterface->fetch();
        $this->assertRegExp('/\nX\n/', $output, 'Missing X output');
        $this->assertContains(
            '1 files with ABNORMAL TERMINATIONS',
            $output,
            'Missing recap title'
        );
        $this->assertContains(
            'SegFaultTestStub.php',
            $output,
            'Missing failing filename'
        );
    }

    public function testWarning()
    {
        $phpunitVersion = new \PHPUnit_Runner_Version();

        if ( ! preg_match('/^5\./', $phpunitVersion->id())) {
            $this->markTestSkipped('PHPUnit < 5 in this env, warnings are not present.');
        }

        $outputInterface = new UnformattedOutputStub();

        $runner = $this->container->get('paraunit.runner.runner');

        $fileArray = array('tests/Stub/MissingProviderTestStub.php');

        $this->assertEquals(
            0,
            $runner->run($fileArray, $outputInterface, new PHPUnitConfigFile('')),
            'Exit code should be 0'
        );

        $output = $outputInterface->fetch();
        $this->assertRegExp('/\nW\n/', $output, 'Missing W output');
        $this->assertContains(
            '1 files with WARNINGS:',
            $output,
            'Missing recap title'
        );
        $this->assertContains(
            'MissingProviderTestStub.php',
            $output,
            'Missing warned filename'
        );
    }

    public function testRegressionFatalErrorsRecognizedAsUnknownResults()
    {
        $outputInterface = new UnformattedOutputStub();

        $runner = $this->container->get('paraunit.runner.runner');

        $fileArray = array('tests/Stub/FatalErrorTestStub.php');

        $this->assertNotEquals(0, $runner->run($fileArray, $outputInterface, new PHPUnitConfigFile('phpunit.xml.dist')), 'Exit code should not be 0');

        $output = $outputInterface->fetch();
        $this->assertRegExp('/\nX\n/', $output, 'Missing X output');
        $this->assertContains('1 files with ABNORMAL TERMINATIONS', $output, 'Missing fatal error recap title');
        $this->assertNotContains('UNKNOWN', $output, 'REGRESSION: fatal error mistaken for unknown result');

    }

    public function testRegressionMissingLogAsUnknownResults()
    {
        $outputInterface = new UnformattedOutputStub();

        $runner = $this->container->get('paraunit.runner.runner');

        $fileArray = array('tests/Stub/ParseErrorTestStub.php');

        $this->assertNotEquals(0, $runner->run($fileArray, $outputInterface, new PHPUnitConfigFile('phpunit.xml.dist')), 'Exit code should not be 0');

        $output = $outputInterface->fetch();
        $this->assertRegExp('/\nX\n/', $output, 'Missing X output');
        $this->assertContains('UNKNOWN', $output);
        $this->assertContains('1 files with ABNORMAL TERMINATIONS', $output, 'Missing abnormal termination recap title');
    }

    public function testRegressionFatalErrorsShouldNotLeakToOutput()
    {
        $outputInterface = new UnformattedOutputStub();

        $runner = $this->container->get('paraunit.runner.runner');

        $fileArray = array('tests/Stub/RaisingNoticeTestStub.php');

        $this->assertNotEquals(0, $runner->run($fileArray, $outputInterface, new PHPUnitConfigFile('phpunit.xml.dist')), 'Exit code should not be 0');
        $output = $outputInterface->getOutput();
        $this->assertGreaterThan(strpos($output, 'Execution time'), strpos($output, 'YOU SHOULD NOT SEE THIS'), 'REGRESSION: garbage output during tests execution (PHP warnigns, var_dumps...)');
    }
}
