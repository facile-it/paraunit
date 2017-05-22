<?php

namespace Tests\Unit\Process;

use Paraunit\Configuration\PHPUnitConfig;
use Paraunit\Process\AbstractParaunitProcess;
use Paraunit\Process\CliCommandInterface;
use Paraunit\Process\ProcessFactory;
use Tests\BaseUnitTestCase;

/**
 * Class ProcessFactoryTest
 * @package Tests\Unit\Process
 */
class ProcessFactoryTest extends BaseUnitTestCase
{
    public function testCreateProcess()
    {
        $phpUnitConfig = $this->prophesize(PHPUnitConfig::class);
        $cliCommand = $this->prophesize(CliCommandInterface::class);
        $cliCommand->getExecutable()->willReturn('executable');
        $cliCommand
            ->getOptions($phpUnitConfig->reveal(), md5('TestTest.php'))
            ->willReturn('--configuration');

        $factory = new ProcessFactory($cliCommand->reveal(), $phpUnitConfig->reveal());

        $process = $factory->createProcess('TestTest.php');

        $this->assertInstanceOf(AbstractParaunitProcess::class, $process);
        $expectedCmdLine = 'executable --configuration TestTest.php';
        $this->assertEquals($expectedCmdLine, $process->getCommandLine());
    }
}
