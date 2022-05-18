<?php

declare(strict_types=1);

namespace Tests\Unit\Process;

use Paraunit\Configuration\ChunkSize;
use Paraunit\Configuration\PHPDbgBinFile;
use Paraunit\Configuration\PHPUnitBinFile;
use Paraunit\Configuration\PHPUnitConfig;
use Paraunit\Configuration\PHPUnitOption;
use Paraunit\Configuration\TempFilenameFactory;
use Paraunit\Parser\JSON\TestHook as Hooks;
use Paraunit\Process\CommandLineWithCoverage;
use Paraunit\Proxy\PcovProxy;
use Paraunit\Proxy\XDebugProxy;
use Tests\BaseUnitTestCase;
use Tests\Stub\StubbedParaunitProcess;

class CommandLineWithCoverageTest extends BaseUnitTestCase
{
    /**
     * @dataProvider extensionProxiesProvider
     *
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
            $this->prophesize(ChunkSize::class)->reveal(),
            $pcovProxy,
            $xdebugProxy,
            $this->prophesize(PHPDbgBinFile::class)->reveal(),
            $tempFileNameFactory->reveal()
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
        $phpunit = $this->prophesize(PHPUnitBinFile::class);
        $phpunit->getPhpUnitBin()
            ->shouldNotBeCalled();

        $cli = new CommandLineWithCoverage(
            $phpunit->reveal(),
            $this->prophesize(ChunkSize::class)->reveal(),
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
     * @dataProvider noExtensionsEnabledProvider
     */
    public function testGetOptions(PcovProxy $pcovProxy, XDebugProxy $xdebugProxy): void
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
            $pcovProxy,
            $xdebugProxy,
            $this->prophesize(PHPDbgBinFile::class)->reveal(),
            $this->prophesize(TempFilenameFactory::class)->reveal()
        );

        $options = $cli->getOptions($config->reveal());

        $this->assertContains('--configuration=/path/to/phpunit.xml', $options);
        $this->assertContains('--opt', $options);
        $this->assertContains('--optVal=value', $options);

        $extensions = array_filter($options, static function (string $a) {
            return 0 === strpos($a, '--extensions');
        });
        $this->assertCount(1, $extensions, 'Missing --extensions from options');
        $registeredExtensions = array_pop($extensions);
        $this->assertNotNull($registeredExtensions);
        $this->assertStringContainsStringIgnoringCase(Hooks\BeforeTest::class, $registeredExtensions);
        $this->assertStringContainsStringIgnoringCase(Hooks\Error::class, $registeredExtensions);
        $this->assertStringContainsStringIgnoringCase(Hooks\Failure::class, $registeredExtensions);
        $this->assertStringContainsStringIgnoringCase(Hooks\Incomplete::class, $registeredExtensions);
        $this->assertStringContainsStringIgnoringCase(Hooks\Risky::class, $registeredExtensions);
        $this->assertStringContainsStringIgnoringCase(Hooks\Skipped::class, $registeredExtensions);
        $this->assertStringContainsStringIgnoringCase(Hooks\Successful::class, $registeredExtensions);
        $this->assertStringContainsStringIgnoringCase(Hooks\Warning::class, $registeredExtensions);
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
            $this->prophesize(XDebugProxy::class)->reveal(),
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
        yield [$this->mockPcov(true), $this->mockXdebug(true), ['php', '-d pcov.enabled=1', 'path/to/phpunit']];
        yield [$this->mockPcov(true), $this->mockXdebug(false), ['php', '-d pcov.enabled=1', 'path/to/phpunit']];
        yield [$this->mockPcov(false), $this->mockXdebug(true), ['php', 'path/to/phpunit']];
    }

    /**
     * @return \Generator<array{PcovProxy, XDebugProxy}>
     */
    public function noExtensionsEnabledProvider(): \Generator
    {
        yield [$this->mockPcov(false), $this->mockXdebug(false)];
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
