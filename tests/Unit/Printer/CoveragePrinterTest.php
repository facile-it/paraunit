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
    public function testOnEngineBeforeStart(bool $enablePhpdbg, bool $enableXdebug, bool $enablePcov, string $expected): void
    {
        $output = new UnformattedOutputStub();

        $printer = new CoveragePrinter(
            $this->mockPhpdbgBin($enablePhpdbg),
            $this->mockXdebugLoaded($enableXdebug),
            $this->mockPcovLoaded($enablePcov),
            $output
        );

        $printer->onEngineBeforeStart();

        $this->assertSame('Coverage driver in use: ' . $expected, trim($output->getOutput()));
    }

    /**
     * @return \Generator<array{bool, bool, bool, string}>
     */
    public static function coverageDriverProvider(): \Generator
    {
        yield [true, true, true, 'Pcov'];
        yield [true, false, true, 'Pcov'];
        yield [false, true, true, 'Pcov'];
        yield [false, false, true, 'Pcov'];
        yield [true, true, false, 'Xdebug'];
        yield [false, true, false, 'Xdebug'];
        yield [true, false, false, 'PHPDBG'];
        yield [false, false, false, 'NO COVERAGE DRIVER FOUND!'];
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
