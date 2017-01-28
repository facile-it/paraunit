<?php

namespace Tests\Unit\Process;

use Paraunit\Process\ProcessBuilderFactory;

/**
 * Class ProcessBuilderFactoryTest
 * @package Tests\Unit\Process
 */
class ProcessBuilderFactoryTest extends \PHPUnit_Framework_TestCase
{
    public function testCreateProcess()
    {
        $phpUnitConfig = $this->prophesize('Paraunit\Configuration\PHPUnitConfig');
        $cliCommand = $this->prophesize('Paraunit\Process\CliCommandInterface');
        $cliCommand->getExecutable()->willReturn('executable');
        $cliCommand
            ->getOptions($phpUnitConfig->reveal())
            ->shouldBeCalled()
            ->willReturn(array('-c' => 'config.xml'));
        $cliCommand
            ->getSpecificOptions('TestTest.php')
            ->shouldBeCalled(1)
            ->willReturn(array('--specific value-for-TestTest.php'));
        $cliCommand
            ->getSpecificOptions('TestTest2.php')
            ->shouldBeCalled(1)
            ->willReturn(array('--specific value-for-TestTest2.php'));

        $factory = new ProcessBuilderFactory($cliCommand->reveal(), $phpUnitConfig->reveal());

        $processBuilder = $factory->create('TestTest.php');

        $this->assertInstanceOf('Symfony\Component\Process\ProcessBuilder', $processBuilder);
        $expectedCmdLine = "'php' 'executable' '-c config.xml' 'TestTest.php' '--specific value-for-TestTest.php'";
        $this->assertEquals($expectedCmdLine, $processBuilder->getProcess()->getCommandLine());

        $processBuilder = $factory->create('TestTest2.php');

        $this->assertInstanceOf('Symfony\Component\Process\ProcessBuilder', $processBuilder);
        $expectedCmdLine = "'php' 'executable' '-c config.xml' 'TestTest2.php' '--specific value-for-TestTest2.php'";
        $this->assertEquals($expectedCmdLine, $processBuilder->getProcess()->getCommandLine());
    }
}
