<?php

declare(strict_types=1);

namespace Tests\Unit\Process;

use Paraunit\Configuration\PHPUnitConfig;
use Paraunit\Configuration\TempFilenameFactory;
use Paraunit\Process\AbstractParaunitProcess;
use Paraunit\Process\CommandLine;
use Paraunit\Process\ProcessFactory;
use Symfony\Component\Process\Process;
use Tests\BaseUnitTestCase;

/**
 * Class ProcessFactoryTest
 * @package Tests\Unit\Process
 */
class ProcessFactoryTest extends BaseUnitTestCase
{
    protected function setUp()
    {
        $process = new Process(['cmd as array']);
        if (\is_array($process->getCommandLine())) {
            $this->markTestSkipped('CommandLine not parsed, we have symfony/process < 3.3');
        }

        parent::setUp();
    }

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
            ->shouldBeCalledTimes(1)
            ->willReturn(['--specific=value-for-TestTest.php']);
        $cliCommand
            ->getSpecificOptions('TestTest2.php')
            ->shouldBeCalledTimes(1)
            ->willReturn(['--specific=value-for-TestTest2.php']);

        $tempFilenameFactory = $this->prophesize(TempFilenameFactory::class);
        $tempFilenameFactory->getPathForLog()
            ->willReturn('/path/for/log/');

        $factory = new ProcessFactory(
            $cliCommand->reveal(),
            $phpUnitConfig->reveal(),
            $tempFilenameFactory->reveal()
        );

        $processWrapper = $factory->create('TestTest.php');

        $this->assertInstanceOf(AbstractParaunitProcess::class, $processWrapper);
        $commandLine = $processWrapper->getCommandLine();
        $this->assertContains('TestTest.php', $commandLine);
        $this->assertContains('--specific=value-for-TestTest.php', $commandLine);

        $processWrapper = $factory->create('TestTest2.php');

        $this->assertInstanceOf(AbstractParaunitProcess::class, $processWrapper);
        $commandLine = $processWrapper->getCommandLine();
        $this->assertContains('TestTest2.php', $commandLine);
        $this->assertContains('--specific=value-for-TestTest2.php', $commandLine);
    }
}
