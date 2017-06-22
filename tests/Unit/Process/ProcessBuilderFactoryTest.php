<?php
declare(strict_types=1);

namespace Tests\Unit\Process;

use Paraunit\Configuration\PHPUnitConfig;
use Paraunit\Configuration\TempFilenameFactory;
use Paraunit\Process\CommandLine;
use Paraunit\Process\ProcessBuilderFactory;
use Symfony\Component\Process\ProcessBuilder;
use Tests\BaseUnitTestCase;

/**
 * Class ProcessBuilderFactoryTest
 * @package Tests\Unit\Process
 */
class ProcessBuilderFactoryTest extends BaseUnitTestCase
{
    public function testCreateProcess()
    {
        $phpUnitConfig = $this->prophesize(PHPUnitConfig::class);
        $cliCommand = $this->prophesize(CommandLine::class);
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

        $tempFilenameFactory = $this->prophesize(TempFilenameFactory::class);
        $tempFilenameFactory->getPathForLog()
            ->willReturn('/path/for/log/');

        $factory = new ProcessBuilderFactory(
            $cliCommand->reveal(),
            $phpUnitConfig->reveal(),
            $tempFilenameFactory->reveal()
        );

        $processBuilder = $factory->create('TestTest.php');

        $this->assertInstanceOf(ProcessBuilder::class, $processBuilder);
        $commandLine = $processBuilder->getProcess()->getCommandLine();
        $this->assertContains('TestTest.php', $commandLine);
        $this->assertContains('--specific=value-for-TestTest.php', $commandLine);

        $processBuilder = $factory->create('TestTest2.php');

        $this->assertInstanceOf(ProcessBuilder::class, $processBuilder);
        $commandLine = $processBuilder->getProcess()->getCommandLine();
        $this->assertContains('TestTest2.php', $commandLine);
        $this->assertContains('--specific=value-for-TestTest2.php', $commandLine);
    }
}
