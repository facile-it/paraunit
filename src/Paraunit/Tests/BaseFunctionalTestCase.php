<?php

namespace Paraunit\Tests;

use Paraunit\Configuration\Paraunit;
use Paraunit\Tests\Stub\StubbedParaProcess;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * Class BaseFunctionalTestCase
 * @package Paraunit\Tests
 */
abstract class BaseFunctionalTestCase extends BaseTestCase
{
    /** @var ContainerBuilder */
    protected $container = null;

    protected function setUp()
    {
        parent::setUp();

        $this->container = Paraunit::buildContainer();
    }

    /**
     * @return StubbedParaProcess
     */
    public function getTestWithSingleError()
    {
        $process = new StubbedParaProcess();
        $process->setExitCode(-1);
        $process->setOutput($this->getOutputFileContent('SingleError.txt'));

        return $process;
    }

    /**
     * @return StubbedParaProcess
     */
    public function getTestWithSingleWarning()
    {
        $process = new StubbedParaProcess();
        $process->setExitCode(-1);
        $process->setOutput($this->getOutputFileContent('SingleWarning.txt'));

        return $process;
    }

    /**
     * @return StubbedParaProcess
     */
    public function getTestWith2Errors2Failures()
    {
        $process = new StubbedParaProcess();
        $process->setExitCode(-1);
        $process->setOutput($this->getOutputFileContent('2Errors2Failures.txt'));

        return $process;
    }

    /**
     * @return StubbedParaProcess
     */
    public function getTestWithParserRegression()
    {
        $process = new StubbedParaProcess();
        $process->setExitCode(-1);
        $process->setOutput($this->getOutputFileContent('2Errors2Failures_parser_regression.txt'));

        return $process;
    }

    /**
     * @return StubbedParaProcess
     */
    public function getTestWithAllGreen()
    {
        $process = new StubbedParaProcess();
        $process->setExitCode(0);
        $process->setOutput($this->getOutputFileContent('AllGreen.txt'));

        return $process;
    }

    /**
     * @return StubbedParaProcess
     */
    public function getTestWithAllGreen5()
    {
        $process = new StubbedParaProcess();
        $process->setExitCode(0);
        $process->setOutput($this->getOutputFileContent('AllGreen5.txt'));

        return $process;
    }

    /**
     * @return StubbedParaProcess
     */
    public function getTestWithFatalError()
    {
        $process = new StubbedParaProcess();
        $process->setExitCode(-1);
        $process->setOutput($this->getOutputFileContent('FatalError.txt'));

        return $process;
    }

    /**
     * @return StubbedParaProcess
     */
    public function getTestWithSegFault()
    {
        if ( ! extension_loaded('sigsegv')) {
            $this->markTestIncomplete('The segfault cannot be reproduced in this environment');
        }

        $process = new StubbedParaProcess();
        $process->setExitCode(-1);
        $process->setOutput($this->getOutputFileContent('SegFault.txt'));

        return $process;
    }

    /**
     * @return StubbedParaProcess
     */
    public function getTestWithVeryLongOutput()
    {
        $process = new StubbedParaProcess();
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
}
