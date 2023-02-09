<?php

declare(strict_types=1);

namespace Tests\Unit\Process;

use Paraunit\Configuration\ChunkSize;
use Paraunit\Configuration\EnvVariables;
use Paraunit\Configuration\PHPUnitConfig;
use Paraunit\Configuration\TempFilenameFactory;
use Paraunit\Coverage\CoverageDriver;
use Paraunit\Process\CommandLine;
use Paraunit\Process\CommandLineWithCoverage;
use Paraunit\Process\Process;
use Paraunit\Process\ProcessFactory;
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

        $this->assertInstanceOf(Process::class, $processWrapper);
        $commandLine = $processWrapper->getCommandLine();
        $this->assertStringContainsString('TestTest.php', $commandLine);
        $this->assertStringContainsString('--specific=value-for-TestTest.php', $commandLine);

        $processWrapper = $factory->create('TestTest2.php');

        $this->assertInstanceOf(Process::class, $processWrapper);
        $commandLine = $processWrapper->getCommandLine();
        $this->assertStringContainsString('TestTest2.php', $commandLine);
        $this->assertStringContainsString('--specific=value-for-TestTest2.php', $commandLine);
    }

    /**
     * @dataProvider coverageDriverDataProvider
     */
    public function testCreateProcessWithCoverageDriver(CoverageDriver $coverageDriver, string $expectedXdebugMode): void
    {
        $phpUnitConfig = $this->prophesize(PHPUnitConfig::class);
        $cliCommand = $this->prophesize(CommandLineWithCoverage::class);
        $cliCommand->getCoverageDriver()
            ->willReturn($coverageDriver);
        $cliCommand->getExecutable()
            ->willReturn(['sapi', 'executable']);
        $cliCommand->getOptions($phpUnitConfig->reveal())
            ->shouldBeCalled()
            ->willReturn(['--configuration=config.xml']);

        $tempFilenameFactory = $this->prophesize(TempFilenameFactory::class);
        $tempFilenameFactory->getPathForLog()
            ->willReturn('/path/for/log/');

        $factory = new ProcessFactory(
            $cliCommand->reveal(),
            $phpUnitConfig->reveal(),
            $tempFilenameFactory->reveal(),
            $this->prophesize(ChunkSize::class)->reveal(),
        );

        $this->assertEquals([
            EnvVariables::LOG_DIR => '/path/for/log/',
            'XDEBUG_MODE' => $expectedXdebugMode,
        ], $factory->environmentVariables);
    }

    /**
     * @return array{CoverageDriver, string}[]
     */
    public static function coverageDriverDataProvider(): array
    {
        return [
            [CoverageDriver::Xdebug, 'coverage'],
            [CoverageDriver::Pcov, 'off'],
            [CoverageDriver::PHPDbg, 'off'],
        ];
    }

    public function testCreateProcessChunked(): void
    {
        $phpUnitConfig = $this->prophesize(PHPUnitConfig::class);
        $cliCommand = $this->prophesize(CommandLine::class);
        $cliCommand->getExecutable()->willReturn(['sapi', 'executable']);
        $cliCommand
            ->getOptions($phpUnitConfig->reveal())
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

        $this->assertInstanceOf(Process::class, $processWrapper);
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
