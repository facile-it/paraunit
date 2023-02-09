<?php

declare(strict_types=1);

namespace Tests\Unit\Process;

use Paraunit\Configuration\ChunkSize;
use Paraunit\Configuration\PHPDbgBinFile;
use Paraunit\Configuration\PHPUnitBinFile;
use Paraunit\Configuration\PHPUnitConfig;
use Paraunit\Configuration\PHPUnitOption;
use Paraunit\Configuration\TempFilenameFactory;
use Paraunit\Coverage\CoverageDriver;
use Paraunit\Process\CommandLineWithCoverage;
use Paraunit\Proxy\PcovProxy;
use Paraunit\Proxy\XDebugProxy;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\BaseUnitTestCase;
use Tests\Stub\StubbedParaunitProcess;

class CommandLineWithCoverageTest extends BaseUnitTestCase
{
    #[DataProvider('extensionProxiesProvider')]
    public function testChooseCoverageDriver(bool $enablePcov, bool $enableXdebug, ?int $xdebugVersion, CoverageDriver $expected): void
    {
        $cli = new CommandLineWithCoverage(
            $this->prophesize(PHPUnitBinFile::class)->reveal(),
            $this->prophesize(ChunkSize::class)->reveal(),
            $this->mockPcov($enablePcov),
            $this->mockXdebug($enableXdebug, $xdebugVersion),
            $this->mockPhpDbg(),
            $this->prophesize(TempFilenameFactory::class)->reveal()
        );

        $this->assertEquals($expected, $cli->getCoverageDriver());
    }

    /**
     * @param array<string, string> $expected
     */
    #[DataProvider('coverageDriverProvider')]
    public function testGetExecutableByDriver(CoverageDriver $coverageDriver, array $expected): void
    {
        $phpunit = $this->prophesize(PHPUnitBinFile::class);
        $phpunit->getPhpUnitBin()
            ->shouldBeCalled()
            ->willReturn('path/to/phpunit');

        $cli = new CommandLineWithCoverage(
            $phpunit->reveal(),
            $this->prophesize(ChunkSize::class)->reveal(),
            $this->mockPcov($coverageDriver === CoverageDriver::Pcov),
            $this->mockXdebug($coverageDriver === CoverageDriver::Xdebug),
            $this->mockPhpDbg(),
            $this->prophesize(TempFilenameFactory::class)->reveal()
        );

        $this->assertEquals($expected, $cli->getExecutable());
    }

    public function testGetExecutableWithDbg(): void
    {
        $cli = new CommandLineWithCoverage(
            $this->mockPHPUnit(),
            $this->prophesize(ChunkSize::class)->reveal(),
            $this->mockPcov(false),
            $this->mockXdebug(false),
            $this->mockPhpDbg(true),
            $this->prophesize(TempFilenameFactory::class)->reveal()
        );

        $expected = [
            '/path/to/phpdbg',
            '-qrr',
            'path/to/phpunit',
        ];

        $this->assertEquals($expected, $cli->getExecutable());
    }

    public function testGetExecutableWithNoDriverAvailable(): void
    {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('No coverage driver seems to be available');

        new CommandLineWithCoverage(
            $this->prophesize(PHPUnitBinFile::class)->reveal(),
            $this->prophesize(ChunkSize::class)->reveal(),
            $this->mockPcov(false),
            $this->mockXdebug(false),
            $this->mockPhpDbg(false),
            $this->prophesize(TempFilenameFactory::class)->reveal()
        );
    }

    #[DataProvider('extensionProxiesProvider')]
    #[DataProvider('noExtensionsEnabledProvider')]
    public function testGetOptions(bool $enablePcov, bool $enableXdebug, ?int $xdebugVersion): void
    {
        $config = $this->prophesize(PHPUnitConfig::class);
        $config->getPhpunitOption('stderr')->willReturn(null);
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
            $this->mockChunkSize(false),
            $this->mockPcov($enablePcov),
            $this->mockXdebug($enableXdebug, $xdebugVersion),
            $this->mockPhpDbg(),
            $this->prophesize(TempFilenameFactory::class)->reveal()
        );

        $options = $cli->getOptions($config->reveal());

        $this->assertContains('--configuration=/path/to/phpunit.xml', $options);
        $this->assertContains('--opt', $options);
        $this->assertContains('--optVal=value', $options);

        $extensions = array_filter($options, static fn (string $a): bool => str_starts_with($a, '--extensions'));
        $this->assertCount(0, $extensions, '--extensions should no longer be used');
    }

    public function testGetOptionsChunkedNotContainsConfiguration(): void
    {
        $config = $this->prophesize(PHPUnitConfig::class);
        $config->getPhpunitOption('stderr')->willReturn(null);
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
            $this->mockChunkSize(true),
            $this->prophesize(PcovProxy::class)->reveal(),
            $this->mockXdebug(true),
            $this->prophesize(PHPDbgBinFile::class)->reveal(),
            $this->prophesize(TempFilenameFactory::class)->reveal()
        );

        $options = $cli->getOptions($config->reveal());

        $this->assertNotContains('--configuration=/path/to/phpunit.xml', $options);
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
            $this->prophesize(ChunkSize::class)->reveal(),
            $this->prophesize(PcovProxy::class)->reveal(),
            $this->mockXdebug(true),
            $this->prophesize(PHPDbgBinFile::class)->reveal(),
            $fileNameFactory->reveal()
        );

        $options = $cli->getSpecificOptions($testFilename);

        $this->assertContains('--coverage-php=/path/to/coverage.php', $options);
    }

    /**
     * @return \Generator<array{bool, bool, int|null, CoverageDriver}>
     */
    public static function extensionProxiesProvider(): \Generator
    {
        yield 'Xdebug 3 + Pcov' => [true, true, 3, CoverageDriver::Xdebug];
        yield 'Xdebug 2 + Pcov' => [true, true, 2, CoverageDriver::Pcov];
        yield 'Pcov only' => [true, false, null, CoverageDriver::Pcov];
        yield 'Xdebug only' => [false, true, null, CoverageDriver::Xdebug];
        yield 'Xdebug off + Pcov off' => [false, false, null, CoverageDriver::PHPDbg];
    }

    /**
     * @return \Generator<array{CoverageDriver, string[]}>
     */
    public static function coverageDriverProvider(): \Generator
    {
        yield [CoverageDriver::Xdebug, ['php', '-d pcov.enabled=0', 'path/to/phpunit']];
        yield [CoverageDriver::Pcov, ['php', '-d pcov.enabled=1', 'path/to/phpunit']];
        yield [CoverageDriver::PHPDbg, ['/path/to/phpdbg', '-qrr', 'path/to/phpunit']];
    }

    /**
     * @return \Generator<array{bool, bool, int|null}>
     */
    public static function noExtensionsEnabledProvider(): \Generator
    {
        yield [false, false, null];
    }

    private function mockPHPUnit(): PHPUnitBinFile
    {
        $phpunit = $this->prophesize(PHPUnitBinFile::class);
        $phpunit->getPhpUnitBin()
            ->shouldBeCalled()
            ->willReturn('path/to/phpunit');

        return $phpunit->reveal();
    }

    private function mockChunkSize(bool $enabled): ChunkSize
    {
        $chunkSize = $this->prophesize(ChunkSize::class);
        $chunkSize->isChunked()
            ->shouldBeCalled()
            ->willReturn($enabled);

        return $chunkSize->reveal();
    }

    private function mockPcov(bool $enabled): PcovProxy
    {
        $pcovProxy = $this->prophesize(PcovProxy::class);
        $pcovProxy->isLoaded()
            ->willReturn($enabled);

        return $pcovProxy->reveal();
    }

    private function mockXdebug(bool $enabled, ?int $majorVersion = null): XDebugProxy
    {
        $majorVersion ??= 3;

        $xdebugProxy = $this->prophesize(XDebugProxy::class);
        $xdebugProxy->isLoaded()
            ->willReturn($enabled);
        $xdebugProxy->getMajorVersion()
            ->willReturn($majorVersion);

        return $xdebugProxy->reveal();
    }

    private function mockPhpDbg(bool $enabled = true): PHPDbgBinFile
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
