<?php

namespace Tests\Unit\Process;

use Paraunit\Process\ProcessFactory;

/**
 * Class ProcessFactoryTest
 * @package Tests\Unit\Process
 */
class ProcessFactoryTest extends \PHPUnit_Framework_TestCase
{
    public function testCreateProcess()
    {
        $phpUnitBin = $this->prophesize('Paraunit\Configuration\PHPUnitBinFile');
        $phpUnitBin->getPhpUnitBin()->shouldBeCalled()->willReturn('phpunit');

        $phpUnitConfig = $this->prophesize('Paraunit\Configuration\PHPUnitConfig');
        $phpUnitConfig->getFileFullPath()->shouldBeCalled()->willReturn('configFile.xml');
        $phpUnitConfig->getPhpunitOptions()->shouldBeCalled()->willReturn(array());

        $fileName = $this->prophesize('Paraunit\Configuration\JSONLogFilename');
        $fileName->generateFromUniqueId(md5('TestTest.php'))->willReturn('log.json');

        $factory = new ProcessFactory($phpUnitBin->reveal(), $fileName->reveal());
        $factory->setConfig($phpUnitConfig->reveal());

        $process = $factory->createProcess('TestTest.php');

        $this->assertInstanceOf('Paraunit\Process\AbstractParaunitProcess', $process);
        $expectedCmdLine = 'phpunit '
            . '--configuration=configFile.xml '
            . '--log-json=log.json '
            . 'TestTest.php';
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
