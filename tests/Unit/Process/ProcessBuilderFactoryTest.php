<?php

namespace Tests\Unit\Process;

use Paraunit\Process\ProcessBuilderFactory;
use Tests\BaseUnitTestCase;
use Paraunit\Configuration\PHPUnitConfig;
use Paraunit\Process\CliCommandInterface;
use Symfony\Component\Process\ProcessBuilder;

/**
 * Class ProcessBuilderFactoryTest
 * @package Tests\Unit\Process
 */
class ProcessBuilderFactoryTest extends BaseUnitTestCase
{
    public function testCreateProcess()
    {
        $phpUnitConfig = $this->prophesize(PHPUnitConfig::class);
        $cliCommand = $this->prophesize(CliCommandInterface::class);
        $cliCommand->getExecutable()->willReturn(['sapi', 'executable']);
        $cliCommand
            ->getOptions($phpUnitConfig->reveal())
            ->shouldBeCalled()
            ->willReturn(['--configuration=config.xml']);
        $cliCommand
            ->getSpecificOptions('TestTest.php')
            ->shouldBeCalled(1)
            ->willReturn(['--specific=value-for-TestTest.php']);
        $cliCommand
            ->getSpecificOptions('TestTest2.php')
            ->shouldBeCalled(1)
            ->willReturn(['--specific=value-for-TestTest2.php']);

        $factory = new ProcessBuilderFactory($cliCommand->reveal(), $phpUnitConfig->reveal());

        $processBuilder = $factory->create('TestTest.php');

        $this->assertInstanceOf(ProcessBuilder::class, $processBuilder);
        $expectedCmdLine = "'sapi' 'executable' '--configuration=config.xml' 'TestTest.php' '--specific=value-for-TestTest.php'";
        $this->assertEquals($expectedCmdLine, $processBuilder->getProcess()->getCommandLine());

        $processBuilder = $factory->create('TestTest2.php');

        $this->assertInstanceOf(ProcessBuilder::class, $processBuilder);
        $expectedCmdLine = "'sapi' 'executable' '--configuration=config.xml' 'TestTest2.php' '--specific=value-for-TestTest2.php'";
        $this->assertEquals($expectedCmdLine, $processBuilder->getProcess()->getCommandLine());
    }
}
