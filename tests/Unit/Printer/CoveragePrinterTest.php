<?php

declare(strict_types=1);

namespace Tests\Unit\Printer;

use Paraunit\Configuration\PHPDbgBinFile;
use Paraunit\Printer\CoveragePrinter;
use Paraunit\Proxy\PcovProxy;
use Paraunit\Proxy\XDebugProxy;
use Tests\BaseUnitTestCase;
use Tests\Stub\UnformattedOutputStub;

class CoveragePrinterTest extends BaseUnitTestCase
{
    /**
     * @dataProvider coverageDriverProvider
     */
    public function testOnEngineBeforeStart(PHPDbgBinFile $phpdbg, XDebugProxy $xdebug, PcovProxy $pcov, string $expected): void
    {
        $output = new UnformattedOutputStub();

        $printer = new CoveragePrinter($phpdbg, $xdebug, $pcov, $output);

        $printer->onEngineBeforeStart();

        $this->assertSame('Coverage driver in use: ' . $expected, trim($output->getOutput()));
    }

    /**
     * @return \Generator<array{PHPDbgBinFile, XDebugProxy, PcovProxy, string}>
     */
    public function coverageDriverProvider(): \Generator
    {
        yield [$this->mockPhpdbgBin(true), $this->mockXdebugLoaded(true), $this->mockPcovLoaded(true), 'Pcov'];
        yield [$this->mockPhpdbgBin(true), $this->mockXdebugLoaded(false), $this->mockPcovLoaded(true), 'Pcov'];
        yield [$this->mockPhpdbgBin(false), $this->mockXdebugLoaded(true), $this->mockPcovLoaded(true), 'Pcov'];
        yield [$this->mockPhpdbgBin(false), $this->mockXdebugLoaded(false), $this->mockPcovLoaded(true), 'Pcov'];
        yield [$this->mockPhpdbgBin(true), $this->mockXdebugLoaded(true), $this->mockPcovLoaded(false), 'Xdebug'];
        yield [$this->mockPhpdbgBin(false), $this->mockXdebugLoaded(true), $this->mockPcovLoaded(false), 'Xdebug'];
        yield [$this->mockPhpdbgBin(true), $this->mockXdebugLoaded(false), $this->mockPcovLoaded(false), 'PHPDBG'];
        yield [$this->mockPhpdbgBin(false), $this->mockXdebugLoaded(false), $this->mockPcovLoaded(false), 'NO COVERAGE DRIVER FOUND!'];
    }

    private function mockPhpdbgBin(bool $shouldReturn): PHPDbgBinFile
    {
        $phpdbgBin = $this->prophesize(PHPDbgBinFile::class);
        $phpdbgBin->isAvailable()
            ->willReturn($shouldReturn);

        return $phpdbgBin->reveal();
    }

    private function mockXdebugLoaded(bool $shouldReturn): XDebugProxy
    {
        $xdebug = $this->prophesize(XDebugProxy::class);
        $xdebug->isLoaded()
            ->willReturn($shouldReturn);

        return $xdebug->reveal();
    }

    private function mockPcovLoaded(bool $shouldReturn): PcovProxy
    {
        $xdebug = $this->prophesize(PcovProxy::class);
        $xdebug->isLoaded()
            ->willReturn($shouldReturn);

        return $xdebug->reveal();
    }
}
