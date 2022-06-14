<?php

declare(strict_types=1);

namespace Paraunit\Process;

use Paraunit\Configuration\PHPDbgBinFile;
use Paraunit\Configuration\PHPUnitBinFile;
use Paraunit\Configuration\TempFilenameFactory;
use Paraunit\Proxy\PcovProxy;
use Paraunit\Proxy\XDebugProxy;

class CommandLineWithCoverage extends CommandLine
{
    /** @var PcovProxy */
    private $pcovProxy;

    /** @var XDebugProxy */
    private $xdebugProxy;

    /** @var PHPDbgBinFile */
    private $phpDbgBinFile;

    /** @var TempFilenameFactory */
    private $filenameFactory;

    public function __construct(
        PHPUnitBinFile $phpUnitBin,
        PcovProxy $pcovProxy,
        XDebugProxy $xdebugProxy,
        PHPDbgBinFile $dbgBinFile,
        TempFilenameFactory $filenameFactory
    ) {
        parent::__construct($phpUnitBin);

        $this->pcovProxy = $pcovProxy;
        $this->xdebugProxy = $xdebugProxy;
        $this->phpDbgBinFile = $dbgBinFile;
        $this->filenameFactory = $filenameFactory;
    }

    /**
     * @throws \RuntimeException
     *
     * @return string[]
     */
    public function getExecutable(): array
    {
        if ($this->pcovProxy->isLoaded()) {
            return ['php', '-d pcov.enabled=1', $this->phpUnitBin->getPhpUnitBin()];
        }

        if ($this->xdebugProxy->isLoaded()) {
            if (3 === $this->xdebugProxy->getMajorVersion()) {
                return ['php', '-d xdebug.mode=coverage', $this->phpUnitBin->getPhpUnitBin()];
            }

            return parent::getExecutable();
        }

        if ($this->phpDbgBinFile->isAvailable()) {
            return [
                $this->phpDbgBinFile->getPhpDbgBin(),
                '-qrr',
                $this->phpUnitBin->getPhpUnitBin(),
            ];
        }

        throw new \RuntimeException('No coverage driver seems to be available; possible choices are Pcov, xdebug or PHPDBG');
    }

    public function getSpecificOptions(string $testFilename): array
    {
        $options = parent::getSpecificOptions($testFilename);
        $options[] = '--coverage-php=' . $this->filenameFactory->getFilenameForCoverage(md5($testFilename));

        return $options;
    }
}
