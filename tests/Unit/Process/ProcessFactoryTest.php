<?php

declare(strict_types=1);

namespace Tests\Unit\Process;

use Paraunit\Configuration\ChunkSize;
use Paraunit\Configuration\PHPUnitConfig;
use Paraunit\Configuration\TempFilenameFactory;
use Paraunit\Process\AbstractParaunitProcess;
use Paraunit\Process\CommandLine;
use Paraunit\Process\ProcessFactory;
use Prophecy\Argument;
use Tests\BaseUnitTestCase;

class ProcessFactoryTest extends BaseUnitTestCase
{
    public function testCreateProcess(): void
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
            $tempFilenameFactory->reveal(),
            $this->mockChunkSize(false)
        );

        $processWrapper = $factory->create('TestTest.php');

        $this->assertInstanceOf(AbstractParaunitProcess::class, $processWrapper);
        $commandLine = $processWrapper->getCommandLine();
        $this->assertStringContainsString('TestTest.php', $commandLine);
        $this->assertStringContainsString('--specific=value-for-TestTest.php', $commandLine);

        $processWrapper = $factory->create('TestTest2.php');

        $this->assertInstanceOf(AbstractParaunitProcess::class, $processWrapper);
        $commandLine = $processWrapper->getCommandLine();
        $this->assertStringContainsString('TestTest2.php', $commandLine);
        $this->assertStringContainsString('--specific=value-for-TestTest2.php', $commandLine);
    }

    public function testCreateProcessChunked(): void
    {
        $phpUnitConfig = $this->prophesize(PHPUnitConfig::class);
        $cliCommand = $this->prophesize(CommandLine::class);
        $cliCommand->getExecutable()->willReturn(['sapi', 'executable']);
        $cliCommand
            ->getOptions($phpUnitConfig->reveal(), Argument::is(['testsuite']))
            ->shouldBeCalled()
            ->willReturn([]);
        $cliCommand
            ->getSpecificOptions('phpunit.xml')
            ->shouldBeCalledTimes(1)
            ->willReturn(['--specific=value-for-phpunit.xml']);

        $tempFilenameFactory = $this->prophesize(TempFilenameFactory::class);
        $tempFilenameFactory->getPathForLog()
            ->willReturn('/path/for/log/');

        $factory = new ProcessFactory(
            $cliCommand->reveal(),
            $phpUnitConfig->reveal(),
            $tempFilenameFactory->reveal(),
            $this->mockChunkSize(true)
        );

        $processWrapper = $factory->create('phpunit.xml');

        $this->assertInstanceOf(AbstractParaunitProcess::class, $processWrapper);
        $commandLine = $processWrapper->getCommandLine();
        $this->assertStringContainsString('--configuration=phpunit.xml', $commandLine);
        $this->assertStringContainsString('--specific=value-for-phpunit.xml', $commandLine);
    }

    private function mockChunkSize(bool $enabled): ChunkSize
    {
        $chunkSize = $this->prophesize(ChunkSize::class);
        $chunkSize->isChunked()
            ->shouldBeCalled()
            ->willReturn($enabled);

        return $chunkSize->reveal();
    }
}
