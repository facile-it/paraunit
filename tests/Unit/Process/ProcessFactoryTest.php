<?php

namespace Tests\Unit\Process;

use Paraunit\Configuration\PHPDbgBinFile;
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

        $phpUnitConfigFile = $this->prophesize('Paraunit\Configuration\PHPUnitConfigFile');
        $phpUnitConfigFile->getFileFullPath()->shouldBeCalled()->willReturn('configFile.xml');

        $fileName = $this->prophesize('Paraunit\Configuration\JSONLogFilename');
        $fileName->generateFromUniqueId(md5('TestTest.php'))->willReturn('log.json');

        $factory = new ProcessFactory($phpUnitBin->reveal(), new PHPDbgBinFile(), $fileName->reveal());
        $factory->setConfigFile($phpUnitConfigFile->reveal());

        $process = $factory->createProcess('TestTest.php');

        $this->assertInstanceOf('Paraunit\Process\AbstractParaunitProcess', $process);
        $expectedCmdLine = 'phpunit '
            . '-c configFile.xml '
            . '--colors=never '
            . '--log-json=log.json '
            . 'TestTest.php';
        $this->assertEquals($expectedCmdLine, $process->getCommandLine());
    }

    public function testCreateProcessThrowsExceptionIfConfigIsMissing()
    {
        $phpUnitBin = $this->prophesize('Paraunit\Configuration\PHPUnitBinFile');
        $fileName = $this->prophesize('Paraunit\Configuration\JSONLogFileName');

        $factory = new ProcessFactory($phpUnitBin->reveal(), new PHPDbgBinFile(), $fileName->reveal());

        $this->setExpectedException('\Exception', 'config missing');
        $factory->createProcess('test');
    }
}
