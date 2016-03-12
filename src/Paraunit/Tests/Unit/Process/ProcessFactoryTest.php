<?php

namespace Paraunit\Tests\Unit\Process;

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

        $fileName = $this->prophesize('Paraunit\Configuration\JSONLogFileName');
        $fileName->generateFromUniqueId(md5('TestTest.php'))->willReturn('log.json');

        $factory = new ProcessFactory($phpUnitBin->reveal(), $fileName->reveal());
        $factory->setConfigFile($phpUnitConfigFile->reveal());

        $process = $factory->createProcess('TestTest.php');

        $this->assertInstanceOf('Paraunit\Process\ParaunitProcessAbstract', $process);
        $expectedCmdLine = 'phpunit '
            . '-c configFile.xml '
            . '--colors=never '
            . '--log-json=log.json '
            . 'TestTest.php '
            . '2>&1';
        $this->assertEquals($expectedCmdLine, $process->getCommandLine());
    }

    public function testCreateProcessThrowsExceptionIfConfigIsMissing()
    {
        $phpUnitBin = $this->prophesize('Paraunit\Configuration\PHPUnitBinFile');
        $fileName = $this->prophesize('Paraunit\Configuration\JSONLogFileName');

        $factory = new ProcessFactory($phpUnitBin->reveal(), $fileName->reveal());

        $this->setExpectedException('\Exception', 'config missing');
        $factory->createProcess('test');
    }
}
