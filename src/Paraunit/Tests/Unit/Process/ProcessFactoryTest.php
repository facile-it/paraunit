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
        $phpUnitBin = $this->prophesize(PHPUnitBinFile::class);
        $phpUnitBin->getPhpUnitBin()->shouldBeCalled()->willReturn('phpunit');

        $phpUnitConfigFile = $this->prophesize(PHPUnitConfigFile::class);
        $phpUnitConfigFile->getFileFullPath()->shouldBeCalled()->willReturn('configFile.xml');

        $factory = new ProcessFactory($phpUnitBin->reveal());
        $factory->setConfigFile($phpUnitConfigFile->reveal());

        $process = $factory->createProcess("TestTest.php");

        $this->assertInstanceOf(ParaunitProcessAbstract::class, $process);
        $this->assertEquals('phpunit -c configFile.xml --colors=never TestTest.php 2>&1', $process->getCommandLine());
    }

    public function testCreateProcessThrowsExceptionIfConfigIsMissing()
    {
        $phpUnitBin = $this->prophesize(PHPUnitBinFile::class);

        $factory = new ProcessFactory($phpUnitBin->reveal());

        $this->setExpectedException(\Exception::class, 'config missing');
        $factory->createProcess('test');
    }
}
