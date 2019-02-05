<?php

declare(strict_types=1);

namespace Paraunit\Process;

use Paraunit\Configuration\PHPDbgBinFile;
use Paraunit\Configuration\PHPUnitBinFile;
use Paraunit\Configuration\PHPUnitConfig;
use Paraunit\Configuration\TempFilenameFactory;

class CommandLineWithCoverage extends CommandLine
{
    /** @var PHPDbgBinFile */
    private $phpDbgBinFile;

    /** @var TempFilenameFactory */
    private $filenameFactory;

    /**
     * @param PHPUnitBinFile $phpUnitBin
     * @param PHPDbgBinFile $dbgBinFile
     * @param TempFilenameFactory $filenameFactory
     */
    public function __construct(
        PHPUnitBinFile $phpUnitBin,
        PHPDbgBinFile $dbgBinFile,
        TempFilenameFactory $filenameFactory
    ) {
        parent::__construct($phpUnitBin);

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
        if ($this->phpDbgBinFile->isAvailable()) {
            return [$this->phpDbgBinFile->getPhpDbgBin()];
        }

        return parent::getExecutable();
    }

    /**
     * @param PHPUnitConfig $config
     *
     * @throws \RuntimeException
     *
     * @return array
     */
    public function getOptions(PHPUnitConfig $config): array
    {
        if (! $this->phpDbgBinFile->isAvailable()) {
            return parent::getOptions($config);
        }

        return array_merge(
            [
                '-qrr',
                $this->phpUnitBin->getPhpUnitBin(),
            ],
            parent::getOptions($config)
        );
    }

    public function getSpecificOptions(string $testFilename): array
    {
        $options = parent::getSpecificOptions($testFilename);
        $options[] = '--coverage-php=' . $this->filenameFactory->getFilenameForCoverage(md5($testFilename));

        return $options;
    }
}
