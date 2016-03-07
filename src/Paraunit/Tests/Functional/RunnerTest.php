<?php

namespace Paraunit\Tests\Functional;

use Paraunit\Configuration\PHPUnitConfigFile;
use Paraunit\Runner\Runner;
use Paraunit\Tests\Stub\ConsoleOutputStub;

/**
 * Class RunnerTest.
 */
class RunnerTest extends \PHPUnit_Framework_TestCase
{
    protected $container = null;

    public function setUp()
    {
        parent::setUp();

        require_once getcwd().'/Container.php';

        $this->container = getContainer();
    }

    public function testAllGreen()
    {
        $outputInterface = new ConsoleOutputStub();

        /** @var Runner $runner */
        $runner = $this->container->get('paraunit.runner.runner');

        $fileArray = array(
            'src/Paraunit/Tests/Stub/ThreeGreenTestStub.php',
        );

        $this->assertEquals(0, $runner->run($fileArray, $outputInterface, new PHPUnitConfigFile('')));

        $dumpster = array(); // PHP 5.3 needs this crap
        $greenCount = preg_match_all('/<ok>.<\/ok>/', $outputInterface->getOutput(), $dumpster);

        $this->assertEquals(3, $greenCount);
    }

    public function testMaxRetryEntityManagerIsClosed()
    {
        $outputInterface = new ConsoleOutputStub();

        /** @var Runner $runner */
        $runner = $this->container->get('paraunit.runner.runner');

        $fileArray = array(
            'src/Paraunit/Tests/Stub/EntityManagerClosedTestStub.php',
        );

        $this->assertNotEquals(0, $runner->run($fileArray, $outputInterface, new PHPUnitConfigFile('')));

        $retryCount = array();
        preg_match_all('/<ok>A<\/ok>/', $outputInterface->getOutput(), $retryCount);
        $errorCount = array();
        preg_match_all('/<error>X|E<\/error>/', $outputInterface->getOutput(), $errorCount);

        $this->assertCount($this->container->getParameter('paraunit.max_retry_count'), $retryCount[0]);
        $this->assertCount(1, $errorCount[0]);
    }

    /**
     * @dataProvider stubFilePathProvider
     */
    public function testMaxRetryDeadlock($stubFilePath)
    {
        $outputInterface = new ConsoleOutputStub();

        $runner = $this->container->get('paraunit.runner.runner');

        $fileArray = array(
            $stubFilePath,
        );

        $this->assertNotEquals(0, $runner->run($fileArray, $outputInterface, new PHPUnitConfigFile('')));

        $retryCount = array();
        preg_match_all('/<ok>A<\/ok>/', $outputInterface->getOutput(), $retryCount);
        $errorCount = array();
        preg_match_all('/<error>X|E<\/error>/', $outputInterface->getOutput(), $errorCount);

        $this->assertCount($this->container->getParameter('paraunit.max_retry_count'), $retryCount[0]);
        $this->assertCount(1, $errorCount[0]);
    }

    public function stubFilePathProvider()
    {
        return array(
            array('src/Paraunit/Tests/Stub/MySQLDeadLockTestStub.php'),
            array('src/Paraunit/Tests/Stub/SQLiteDeadLockTestStub.php'),
        );
    }

    public function testSegFault()
    {
        if (!extension_loaded('sigsegv')) {
            $this->markTestSkipped('The segfault cannot be reproduced in this environment');
        }

        $outputInterface = new ConsoleOutputStub();

        $runner = $this->container->get('paraunit.runner.runner');

        $fileArray = array(
            'src/Paraunit/Tests/Stub/SegFaultTestStub.php',
        );

        $this->assertNotEquals(
            0,
            $runner->run($fileArray, $outputInterface, new PHPUnitConfigFile('')),
            'Exit code should not be 0'
        );

        $this->assertContains('<segfault>X</segfault>', $outputInterface->getOutput(), 'Missing X output');
        $this->assertContains(
            '1 files with SEGMENTATION FAULTS:',
            $outputInterface->getOutput(),
            'Missing recap title'
        );
        $this->assertContains(
            'SegFaultTestStub.php',
            $outputInterface->getOutput(),
            'Missing failing filename'
        );
    }

    /**
     * @group this
     */
    public function testWarning()
    {
        $phpunitVersion = new \PHPUnit_Runner_Version();

        if ( ! preg_match('/^5\./', $phpunitVersion->id())) {
            $this->markTestSkipped('PHPUnit < 5 in this env, warnings are not present.');
        }

        $outputInterface = new ConsoleOutputStub();

        $runner = $this->container->get('paraunit.runner.runner');

        $fileArray = array(
            'src/Paraunit/Tests/Stub/MissingProviderTestStub.php',
        );

        $this->assertEquals(
            0,
            $runner->run($fileArray, $outputInterface, new PHPUnitConfigFile('')),
            'Exit code should be 0'
        );

        $this->assertContains('<warning>W</warning>', $outputInterface->getOutput(), 'Missing W output');
        $this->assertContains(
            '1 files with WARNINGS:',
            $outputInterface->getOutput(),
            'Missing recap title'
        );
        $this->assertContains(
            '<warning>MissingProviderTestStub.php</warning>',
            $outputInterface->getOutput(),
            'Missing warned filename'
        );
    }

    public function testRegressionFatalErrorsRecognizedAsUnknownResults()
    {
        $outputInterface = new ConsoleOutputStub();

        $runner = $this->container->get('paraunit.runner.runner');

        $fileArray = array(
            'src/Paraunit/Tests/Stub/FatalErrorTestStub.php',
        );

        $this->assertNotEquals(0, $runner->run($fileArray, $outputInterface, new PHPUnitConfigFile('phpunit.xml.dist')), 'Exit code should not be 0');

        $this->assertContains('<fatal>X</fatal>', $outputInterface->getOutput(), 'Missing X output');
        $this->assertContains('1 files with FATAL ERRORS:', $outputInterface->getOutput(), 'Missing fatal error recap title');
        $this->assertNotContains('1 files with UNKNOWN STATUS:', $outputInterface->getOutput(), 'REGRESSION: fatal error mistaken for unknown result');

    }
}
