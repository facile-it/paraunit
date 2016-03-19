<?php

namespace Tests\Unit\Process;

use Paraunit\Configuration\PHPDbgBinFile;
use Paraunit\Process\ProcessFactory;
use Prophecy\Argument;

/**
 * Class ProcessFactoryTest
 * @package Tests\Unit\Process
 */
class ProcessFactoryTest extends \PHPUnit_Framework_TestCase
{
    public function testCreateProcess()
    {
        $phpunitConfigFile = $this->prophesize('Paraunit\Configuration\PHPUnitConfigFile');
        $cliCommand = $this->prophesize('Paraunit\Process\CliCommandInterface');
        $cliCommand->getExecutable()->willReturn('executable');
        $cliCommand
            ->getOptions($phpunitConfigFile->reveal(), md5('TestTest.php'))
            ->willReturn('--configuration');

        $factory = new ProcessFactory($cliCommand->reveal());
        $factory->setPHPUnitConfigFile($phpunitConfigFile->reveal());

        $process = $factory->createProcess('TestTest.php');

        $this->assertInstanceOf('Paraunit\Process\AbstractParaunitProcess', $process);
        $expectedCmdLine = 'executable --configuration TestTest.php';
        $this->assertEquals($expectedCmdLine, $process->getCommandLine());
    }
}
