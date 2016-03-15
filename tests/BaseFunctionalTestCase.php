<?php

namespace Tests;

use Paraunit\Configuration\Paraunit;
use Paraunit\File\Cleaner;
use Paraunit\File\TempDirectory;
use Tests\Stub\StubbedParaunitProcess;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * Class BaseFunctionalTestCase
 * @package Paraunit\Tests
 */
abstract class BaseFunctionalTestCase extends \PHPUnit_Framework_TestCase
{
    /** @var ContainerBuilder */
    protected $container = null;

    protected function setUp()
    {
        parent::setUp();

        $this->container = Paraunit::buildContainer();
        $this->cleanUpTempDirForThisExecution();
    }

    protected function tearDown()
    {
        $this->cleanUpTempDirForThisExecution();

        parent::tearDown();
    }

    /**
     * @return StubbedParaunitProcess
     */
    public function getTestWithSingleError()
    {
        $process = new StubbedParaunitProcess();
        $process->setExitCode(-1);
        $process->setOutput($this->getOutputFileContent('SingleError.txt'));

        return $process;
    }

    /**
     * @return StubbedParaunitProcess
     */
    public function getTestWithSingleWarning()
    {
        $process = new StubbedParaunitProcess();
        $process->setExitCode(-1);
        $process->setOutput($this->getOutputFileContent('SingleWarning.txt'));

        return $process;
    }

    /**
     * @return StubbedParaunitProcess
     */
    public function getTestWith2Errors2Failures()
    {
        $process = new StubbedParaunitProcess();
        $process->setExitCode(-1);
        $process->setOutput($this->getOutputFileContent('2Errors2Failures.txt'));

        return $process;
    }

    /**
     * @return StubbedParaunitProcess
     */
    public function getTestWithParserRegression()
    {
        $process = new StubbedParaunitProcess();
        $process->setExitCode(-1);
        $process->setOutput($this->getOutputFileContent('2Errors2Failures_parser_regression.txt'));

        return $process;
    }

    /**
     * @return StubbedParaunitProcess
     */
    public function getTestWithAllGreen()
    {
        $process = new StubbedParaunitProcess();
        $process->setExitCode(0);
        $process->setOutput($this->getOutputFileContent('AllGreen.txt'));

        return $process;
    }

    /**
     * @return StubbedParaunitProcess
     */
    public function getTestWithAllGreen5()
    {
        $process = new StubbedParaunitProcess();
        $process->setExitCode(0);
        $process->setOutput($this->getOutputFileContent('AllGreen5.txt'));

        return $process;
    }

    /**
     * @return StubbedParaunitProcess
     */
    public function getTestWithFatalError()
    {
        $process = new StubbedParaunitProcess();
        $process->setExitCode(-1);
        $process->setOutput($this->getOutputFileContent('FatalError.txt'));

        return $process;
    }

    /**
     * @return StubbedParaunitProcess
     */
    public function getTestWithSegFault()
    {
        if ( ! extension_loaded('sigsegv')) {
            $this->markTestIncomplete('The segfault cannot be reproduced in this environment');
        }

        $process = new StubbedParaunitProcess();
        $process->setExitCode(-1);
        $process->setOutput($this->getOutputFileContent('SegFault.txt'));

        return $process;
    }

    /**
     * @return StubbedParaunitProcess
     */
    public function getTestWithVeryLongOutput()
    {
        $process = new StubbedParaunitProcess();
        $process->setExitCode(0);
        $process->setOutput($this->getOutputFileContent('VeryLongOutput.txt'));

        return $process;
    }

    /**
     * @param $filename
     *
     * @return string
     */
    protected function getOutputFileContent($filename)
    {
        return file_get_contents(__DIR__ . '/Stub/PHPUnitOutput/' . $filename);
    }

    private function cleanUpTempDirForThisExecution()
    {
        /** @var TempDirectory $tempDirectory */
        $tempDirectory = $this->container->get('paraunit.file.temp_directory');
        Cleaner::cleanUpDir($tempDirectory->getTempDirForThisExecution());
    }
}
