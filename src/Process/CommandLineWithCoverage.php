<?php

declare(strict_types=1);

namespace Paraunit\Process;

use Paraunit\Configuration\PHPDbgBinFile;
use Paraunit\Configuration\PHPUnitBinFile;
use Paraunit\Configuration\PHPUnitConfig;
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
            return ['php', '-d pcov.enable=1', $this->phpUnitBin->getPhpUnitBin()];
        }

        if ($this->xdebugProxy->isLoaded()) {
            return parent::getExecutable();
        }

        if ($this->phpDbgBinFile->isAvailable()) {
            return [$this->phpDbgBinFile->getPhpDbgBin()];
        }

        throw new \RuntimeException('No coverage driver seems to be available; possible choices are Pcov, xdebug or PHPDBG');
    }

    /**
     * @throws \RuntimeException
     *
     * @return string[]
     */
    public function getOptions(PHPUnitConfig $config): array
    {
        if ($this->pcovProxy->isLoaded() || $this->xdebugProxy->isLoaded()) {
            return parent::getOptions($config);
        }

        if ($this->phpDbgBinFile->isAvailable()) {
            return array_merge(
                [
                    '-qrr',
                    $this->phpUnitBin->getPhpUnitBin(),
                ],
                parent::getOptions($config)
            );
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
