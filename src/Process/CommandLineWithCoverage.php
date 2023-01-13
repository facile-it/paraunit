<?php

declare(strict_types=1);

namespace Paraunit\Process;

use Paraunit\Configuration\ChunkSize;
use Paraunit\Configuration\PHPDbgBinFile;
use Paraunit\Configuration\PHPUnitBinFile;
use Paraunit\Configuration\TempFilenameFactory;
use Paraunit\Coverage\CoverageDriver;
use Paraunit\Proxy\PcovProxy;
use Paraunit\Proxy\XDebugProxy;

class CommandLineWithCoverage extends CommandLine
{
    private readonly CoverageDriver $coverageDriver;

    public function __construct(
        PHPUnitBinFile $phpUnitBin,
        ChunkSize $chunkSize,
        private readonly PcovProxy $pcovProxy,
        private readonly XDebugProxy $xdebugProxy,
        private readonly PHPDbgBinFile $phpDbgBinFile,
        private readonly TempFilenameFactory $filenameFactory
    ) {
        parent::__construct($phpUnitBin, $chunkSize);

        $this->coverageDriver = $this->chooseCoverageDriver();
    }

    private function chooseCoverageDriver(): CoverageDriver
    {
        if ($this->xdebugProxy->isLoaded() && $this->xdebugProxy->getMajorVersion() > 2) {
            return CoverageDriver::Xdebug;
        }

        if ($this->pcovProxy->isLoaded()) {
            return CoverageDriver::Pcov;
        }

        if ($this->xdebugProxy->isLoaded()) {
            return CoverageDriver::Xdebug;
        }

        if ($this->phpDbgBinFile->isAvailable()) {
            return CoverageDriver::PHPDbg;
        }

        throw new \RuntimeException('No coverage driver seems to be available; possible choices are Pcov, Xdebug 2/3 or PHPDBG');
    }

    public function getCoverageDriver(): CoverageDriver
    {
        return $this->coverageDriver;
    }

    /**
     * @throws \RuntimeException
     *
     * @return string[]
     */
    public function getExecutable(): array
    {
        return match ($this->coverageDriver) {
            CoverageDriver::Xdebug => ['php', '-d pcov.enabled=0', $this->phpUnitBin->getPhpUnitBin()],
            CoverageDriver::Pcov => ['php', '-d pcov.enabled=1', $this->phpUnitBin->getPhpUnitBin()],
            CoverageDriver::PHPDbg => [
                $this->phpDbgBinFile->getPhpDbgBin(),
                '-qrr',
                $this->phpUnitBin->getPhpUnitBin(),
            ],
        };
    }

    public function getSpecificOptions(string $testFilename): array
    {
        $options = parent::getSpecificOptions($testFilename);
        $options[] = '--coverage-php=' . $this->filenameFactory->getFilenameForCoverage(md5($testFilename));

        return $options;
    }
}
