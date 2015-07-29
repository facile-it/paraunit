<?php

namespace Paraunit\Tests\Functional;

use Paraunit\Runner\Runner;
use Paraunit\Tests\Stub\ConsoleOutputStub;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

/**
 * Class RunnerTest.
 */
class RunnerTest extends \PHPUnit_Framework_TestCase
{
    protected $container = null;

    public function setUp()
    {
        parent::setUp();

        $container = new ContainerBuilder();

        $loader = new YamlFileLoader($container, new FileLocator(__DIR__.'/../../Resources/config/'));
        $loader->load('services.yml');
        $this->container = $container;
    }

    public function testMaxRetryEntityManagerIsClosed()
    {
        $outputInterface = new ConsoleOutputStub();

        /** @var Runner $runner */
        $runner = $this->container->get('facile.cbr.parallel_test_bundle.runner.runner');

        $fileArray = array(
            'src/Paraunit/Tests/Stub/EntityManagerClosedTestStub.php',
        );

        $this->assertNotEquals(0, $runner->run($fileArray, $outputInterface, 'phpunit.xml.dist'));

        $retryCount = array();
        preg_match_all("/<ok>A<\/ok>/", $outputInterface->getOutput(), $retryCount);
        $errorCount = array();
        preg_match_all("/<error>E<\/error>/", $outputInterface->getOutput(), $errorCount);

        $this->assertCount($this->container->getParameter('paraunit.max_retry_count'), $retryCount[0]);
        $this->assertCount(1, $errorCount[0]);
    }

    public function testMaxRetryDeadlock()
    {
        $outputInterface = new ConsoleOutputStub();

        $runner = $this->container->get('facile.cbr.parallel_test_bundle.runner.runner');

        $fileArray = array(
            'src/Paraunit/Tests/Stub/DeadLockTestStub.php',
        );

        $this->assertNotEquals(0, $runner->run($fileArray, $outputInterface, 'phpunit.xml.dist'));

        $retryCount = array();
        preg_match_all("/<ok>A<\/ok>/", $outputInterface->getOutput(), $retryCount);
        $errorCount = array();
        preg_match_all("/<error>E<\/error>/", $outputInterface->getOutput(), $errorCount);

        $this->assertCount($this->container->getParameter('paraunit.max_retry_count'), $retryCount[0]);
        $this->assertCount(1, $errorCount[0]);
    }

    public function testSegFault()
    {
        $this->markTestSkipped();

        $outputInterface = new ConsoleOutputStub();

        $runner = $this->container->get('facile.cbr.parallel_test_bundle.runner.runner');

        $fileArray = array(
            'src/Paraunit/Tests/Stub/SegFaultTestStub.php',
        );

        $this->assertNotEquals(0, $runner->run($fileArray, $outputInterface, 'phpunit.xml.dist'));

        $errorCount = array();
        preg_match_all("/<error>X<\/error>/", $outputInterface->getOutput(), $errorCount);
        $this->assertCount(1, $errorCount[0], 'Manca la X');

        $fileRecap = array();
        preg_match_all('/1 files with SEGMENTATION FAULTS:/', $outputInterface->getOutput(), $fileRecap);
        $this->assertCount(1, $fileRecap[0], 'Manca il titolo del recap');
        preg_match_all('/<error>SegFaultTestStub.php<\/error>/', $outputInterface->getOutput(), $fileRecap);
        $this->assertCount(1, $fileRecap[0], 'Manca il nome del file che ha fallito');
    }
}
