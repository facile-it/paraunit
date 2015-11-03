<?php

namespace Paraunit\Tests\Functional;

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

        $runner->setPhpunitConfigFile('phpunit.xml.dist');
        $runner->setMaxProcessNumber(10);
        $this->assertEquals(0, $runner->run($fileArray, $outputInterface));

        $dumpster = array(); // PHP 5.3 needs this crap
        $greenCount = preg_match_all("/<ok>.<\/ok>/", $outputInterface->getOutput(), $dumpster);

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

        $runner->setPhpunitConfigFile('phpunit.xml.dist');
        $runner->setMaxProcessNumber(10);
        $this->assertNotEquals(0, $runner->run($fileArray, $outputInterface));

        $retryCount = array();
        preg_match_all("/<ok>A<\/ok>/", $outputInterface->getOutput(), $retryCount);
        $errorCount = array();
        preg_match_all("/<error>X|E<\/error>/", $outputInterface->getOutput(), $errorCount);

        $this->assertCount($this->container->getParameter('paraunit.max_retry_count'), $retryCount[0]);
        $this->assertCount(1, $errorCount[0]);
    }

    public function testMaxRetryDeadlock()
    {
        $outputInterface = new ConsoleOutputStub();

        /** @var Runner $runner */
        $runner = $this->container->get('paraunit.runner.runner');

        $fileArray = array(
            'src/Paraunit/Tests/Stub/DeadLockTestStub.php',
        );

        $runner->setPhpunitConfigFile('phpunit.xml.dist');
        $runner->setMaxProcessNumber(10);
        $this->assertNotEquals(0, $runner->run($fileArray, $outputInterface, 'phpunit.xml.dist'));

        $retryCount = array();
        preg_match_all("/<ok>A<\/ok>/", $outputInterface->getOutput(), $retryCount);
        $errorCount = array();
        preg_match_all("/<error>X|E<\/error>/", $outputInterface->getOutput(), $errorCount);

        $this->assertCount($this->container->getParameter('paraunit.max_retry_count'), $retryCount[0]);
        $this->assertCount(1, $errorCount[0]);
    }

    public function testSegFault()
    {
        if (!extension_loaded('sigsegv')) {
            $this->markTestIncomplete('The segfault cannot be reproduced in this environment');
        }

        $outputInterface = new ConsoleOutputStub();

        /** @var Runner $runner */
        $runner = $this->container->get('paraunit.runner.runner');

        $fileArray = array(
            'src/Paraunit/Tests/Stub/SegFaultTestStub.php',
        );

        $runner->setPhpunitConfigFile('phpunit.xml.dist');
        $runner->setMaxProcessNumber(10);

        $this->assertNotEquals(
            0,
            $runner->run($fileArray, $outputInterface),
            'Exit code should not be 0'
        );

        $this->assertContains('<error>X</error>', $outputInterface->getOutput(), 'Missing X output');
        $this->assertContains(
            '1 files with SEGMENTATION FAULTS:',
            $outputInterface->getOutput(),
            'Missing recap title'
        );
        $this->assertContains(
            '<error>SegFaultTestStub.php</error>',
            $outputInterface->getOutput(),
            'Missing failing filename'
        );
    }
}
