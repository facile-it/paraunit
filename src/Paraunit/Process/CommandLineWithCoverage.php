<?php
declare(strict_types=1);

namespace Paraunit\Process;

use Paraunit\Configuration\PHPDbgBinFile;
use Paraunit\Configuration\PHPUnitBinFile;
use Paraunit\Configuration\PHPUnitConfig;
use Paraunit\Configuration\TempFilenameFactory;

/**
 * Class CommandLineWithCoverage
 * @package Paraunit\Process
 */
class CommandLineWithCoverage extends CommandLine implements CliCommandInterface
{
    /** @var PHPDbgBinFile */
    private $phpDbgBinFile;

    /** @var TempFilenameFactory */
    private $filenameFactory;

    /**
     * TestCliCommand constructor.
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

    public function getExecutable(): string
    {
        if ($this->phpDbgBinFile->isAvailable()) {
            return $this->phpDbgBinFile->getPhpDbgBin();
        }

        return parent::getExecutable();
    }

    /**
     * @param PHPUnitConfig $config
     * @return array
     */
    public function getOptions(PHPUnitConfig $config): array
    {
        $options = parent::getOptions($config);
        if ($this->phpDbgBinFile->isAvailable()) {
            $options[] = '-qrr ' . $this->phpUnitBin->getPhpUnitBin();
        }

        return $options;
    }

    public function getSpecificOptions(string $testFilename)
    {
        $options = parent::getSpecificOptions($testFilename);
        $options[] = '--coverage ' . $this->filenameFactory->getFilenameForCoverage(md5($testFilename));

        return $options;
    }
}
