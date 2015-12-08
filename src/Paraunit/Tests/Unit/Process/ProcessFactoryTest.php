<?php

namespace Paraunit\Tests\Unit\Process;

use Paraunit\Configuration\PHPUnitBinFile;
use Paraunit\Configuration\PHPUnitConfigFile;
use Paraunit\Process\ParaunitProcessAbstract;
use Paraunit\Process\ProcessFactory;

/**
 * Class ProcessFactoryTest
 * @package Paraunit\Tests\Unit\Process
 */
class ProcessFactoryTest extends \PHPUnit_Framework_TestCase
{
    public function testCreateProcess()
    {
        $phpUnitBin = $this->prophesize('Paraunit\Configuration\PHPUnitBinFile');
        $phpUnitBin->getPhpUnitBin()->shouldBeCalled()->willReturn('phpunit');

        $phpUnitConfigFile = $this->prophesize('Paraunit\Configuration\PHPUnitConfigFile');
        $phpUnitConfigFile->getFileFullPath()->shouldBeCalled()->willReturn('configFile.xml');

        $factory = new ProcessFactory($phpUnitBin->reveal());
        $factory->setConfigFile($phpUnitConfigFile->reveal());

        $process = $factory->createProcess("TestTest.php");

        $this->assertInstanceOf('Paraunit\Process\ParaunitProcessAbstract', $process);
        $this->assertEquals('phpunit -c configFile.xml --colors=never TestTest.php 2>&1', $process->getCommandLine());
    }

    public function testCreateProcessThrowsExceptionIfConfigIsMissing()
    {
        $phpUnitBin = $this->prophesize('Paraunit\Configuration\PHPUnitBinFile');

        $factory = new ProcessFactory($phpUnitBin->reveal());

        $this->setExpectedException('\Exception', 'config missing');
        $factory->createProcess('test');
    }
}
