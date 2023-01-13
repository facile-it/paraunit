<?php

declare(strict_types=1);

namespace Tests\Unit\Printer;

use Paraunit\Configuration\PHPDbgBinFile;
use Paraunit\Coverage\CoverageDriver;
use Paraunit\Printer\CoveragePrinter;
use Paraunit\Process\CommandLineWithCoverage;
use Paraunit\Proxy\PcovProxy;
use Paraunit\Proxy\XDebugProxy;
use Tests\BaseUnitTestCase;
use Tests\Stub\UnformattedOutputStub;

class CoveragePrinterTest extends BaseUnitTestCase
{
    /**
     * @dataProvider coverageDriverProvider
     */
    public function testOnEngineBeforeStart(CoverageDriver $coverageDriver, string $expected): void
    {
        $output = new UnformattedOutputStub();
        $commandLine = $this->prophesize(CommandLineWithCoverage::class);
        $commandLine->getCoverageDriver()
            ->willReturn($coverageDriver);

        $printer = new CoveragePrinter(
            $commandLine->reveal(),
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
        yield [CoverageDriver::Pcov, 'Pcov'];
        yield [CoverageDriver::Xdebug, 'Xdebug'];
        yield [CoverageDriver::PHPDbg, 'PHPDBG'];
    }
}
