<?php

declare(strict_types=1);

namespace Tests\Unit\Process;

use Paraunit\Configuration\PHPDbgBinFile;
use Paraunit\Configuration\PHPUnitBinFile;
use Paraunit\Configuration\PHPUnitConfig;
use Paraunit\Configuration\PHPUnitOption;
use Paraunit\Configuration\TempFilenameFactory;
use Paraunit\Parser\JSON\LogPrinter;
use Paraunit\Process\CommandLineWithCoverage;
use Paraunit\Proxy\PcovProxy;
use Paraunit\Proxy\XDebugProxy;
use Tests\BaseUnitTestCase;
use Tests\Stub\StubbedParaunitProcess;

class CommandLineWithCoverageTest extends BaseUnitTestCase
{
    /**
     * @dataProvider extensionProxiesProvider
     * @param string[] $expected
     */
    public function testGetExecutableWithDriverByExtension(PcovProxy $pcovProxy, XDebugProxy $xdebugProxy, array $expected): void
    {
        $phpunit = $this->prophesize(PHPUnitBinFile::class);
        $phpunit->getPhpUnitBin()
            ->shouldBeCalled()
            ->willReturn('path/to/phpunit');
        $tempFileNameFactory = $this->prophesize(TempFilenameFactory::class);

        $cli = new CommandLineWithCoverage(
            $phpunit->reveal(),
            $pcovProxy,
            $xdebugProxy,
            $this->prophesize(PHPDbgBinFile::class)->reveal(),
            $tempFileNameFactory->reveal()
        );

        $this->assertEquals($expected, $cli->getExecutable());
    }

    public function testGetExecutableWithDbg(): void
    {
        $phpunit = $this->prophesize(PHPUnitBinFile::class);
        $phpunit->getPhpUnitBin()
            ->shouldNotBeCalled();

        $cli = new CommandLineWithCoverage(
            $phpunit->reveal(),
            $this->mockPcov(false),
            $this->mockXdebug(false),
            $this->mockPhpDbg(true),
            $this->prophesize(TempFilenameFactory::class)->reveal()
        );

        $this->assertEquals(['/path/to/phpdbg'], $cli->getExecutable());
    }

    public function testGetExecutableWithNoDriverAvailable(): void
    {
        $phpunit = $this->prophesize(PHPUnitBinFile::class);
        $phpunit->getPhpUnitBin()
            ->shouldNotBeCalled();

        $cli = new CommandLineWithCoverage(
            $phpunit->reveal(),
            $this->mockPcov(false),
            $this->mockXdebug(false),
            $this->mockPhpDbg(false),
            $this->prophesize(TempFilenameFactory::class)->reveal()
        );

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('No coverage driver');

        $cli->getExecutable();
    }

    /**
     * @dataProvider extensionProxiesProvider
     */
    public function testGetOptionsWithDriverByExtension(PcovProxy $pcovProxy, XDebugProxy $xdebugProxy): void
    {
        $config = $this->prophesize(PHPUnitConfig::class);
        $config->getFileFullPath()->willReturn('/path/to/phpunit.xml');
        $optionWithValue = new PHPUnitOption('optVal');
        $optionWithValue->setValue('value');
        $config->getPhpunitOptions()
            ->willReturn([
                new PHPUnitOption('opt', false),
                $optionWithValue,
            ]);

        $phpunit = $this->prophesize(PHPUnitBinFile::class);

        $cli = new CommandLineWithCoverage(
            $phpunit->reveal(),
            $pcovProxy,
            $xdebugProxy,
            $this->prophesize(PHPDbgBinFile::class)->reveal(),
            $this->prophesize(TempFilenameFactory::class)->reveal()
        );

        $options = $cli->getOptions($config->reveal());

        $this->assertContains('--configuration=/path/to/phpunit.xml', $options);
        $this->assertContains('--printer=' . LogPrinter::class, $options);
        $this->assertContains('--opt', $options);
        $this->assertContains('--optVal=value', $options);
    }

    public function testGetOptionsWithDbg(): void
    {
        $config = $this->prophesize(PHPUnitConfig::class);
        $config->getFileFullPath()
            ->willReturn('/path/to/phpunit.xml');
        $config->getPhpunitOptions()
            ->willReturn([
                new PHPUnitOption('opt', false),
            ]);

        $phpunit = $this->prophesize(PHPUnitBinFile::class);
        $phpunit->getPhpUnitBin()
            ->shouldBeCalled()
            ->willReturn('path/to/phpunit');

        $cli = new CommandLineWithCoverage(
            $phpunit->reveal(),
            $this->mockPcov(false),
            $this->mockXdebug(false),
            $this->mockPhpDbg(true),
            $this->prophesize(TempFilenameFactory::class)->reveal()
        );

        $options = $cli->getOptions($config->reveal());

        $this->assertContains('-qrr', $options);
        $this->assertEquals('-qrr', $options[0], '-qrr option needs to be the first one!');
        $this->assertContains('path/to/phpunit', $options);
        $this->assertEquals('path/to/phpunit', $options[1], 'PHPUnit bin path must follow the -qrr option');
        $this->assertContains('--configuration=/path/to/phpunit.xml', $options);
        $this->assertContains('--printer=' . LogPrinter::class, $options);
        $this->assertContains('--opt', $options);
    }

    public function testGetOptionsWithNoDriverAvailable(): void
    {
        $cli = new CommandLineWithCoverage(
            $this->prophesize(PHPUnitBinFile::class)->reveal(),
            $this->mockPcov(false),
            $this->mockXdebug(false),
            $this->mockPhpDbg(false),
            $this->prophesize(TempFilenameFactory::class)->reveal()
        );

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('No coverage driver');

        $cli->getOptions($this->prophesize(PHPUnitConfig::class)->reveal());
    }

    public function testGetSpecificOptions(): void
    {
        $testFilename = 'TestTest.php';
        $process = new StubbedParaunitProcess($testFilename);
        $uniqueId = $process->getUniqueId();
        $fileNameFactory = $this->prophesize(TempFilenameFactory::class);
        $fileNameFactory->getFilenameForCoverage($uniqueId)
            ->willReturn('/path/to/coverage.php');

        $cli = new CommandLineWithCoverage(
            $this->prophesize(PHPUnitBinFile::class)->reveal(),
            $this->prophesize(PcovProxy::class)->reveal(),
            $this->prophesize(XDebugProxy::class)->reveal(),
            $this->prophesize(PHPDbgBinFile::class)->reveal(),
            $fileNameFactory->reveal()
        );

        $options = $cli->getSpecificOptions($testFilename);

        $this->assertContains('--coverage-php=/path/to/coverage.php', $options);
    }

    /**
     * @return \Generator<array{PcovProxy, XDebugProxy, string[]}>
     */
    public function extensionProxiesProvider(): \Generator
    {
        yield [$this->mockPcov(true), $this->mockXdebug(true), ['php', '-d pcov.enable=1', 'path/to/phpunit']];
        yield [$this->mockPcov(true), $this->mockXdebug(false), ['php', '-d pcov.enable=1', 'path/to/phpunit']];
        yield [$this->mockPcov(false), $this->mockXdebug(true), ['php', 'path/to/phpunit']];
    }

    private function mockPcov(bool $enabled): PcovProxy
    {
        $pcovProxy = $this->prophesize(PcovProxy::class);
        $pcovProxy->isLoaded()
            ->willReturn($enabled);

        return $pcovProxy->reveal();
    }

    private function mockXdebug(bool $enabled): XDebugProxy
    {
        $xdebugProxy = $this->prophesize(XDebugProxy::class);
        $xdebugProxy->isLoaded()
            ->willReturn($enabled);

        return $xdebugProxy->reveal();
    }

    private function mockPhpDbg(bool $enabled): PHPDbgBinFile
    {
        $phpDbg = $this->prophesize(PHPDbgBinFile::class);
        $phpDbg->isAvailable()
            ->willReturn($enabled);

        if ($enabled) {
            $phpDbg->getPhpDbgBin()
                ->willReturn('/path/to/phpdbg');
        }

        return $phpDbg->reveal();
    }
}
