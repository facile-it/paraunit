<?php
declare(strict_types=1);

namespace Paraunit\Process;

use Paraunit\Configuration\PHPDbgBinFile;
use Paraunit\Configuration\PHPUnitBinFile;
use Paraunit\Configuration\PHPUnitConfig;
use Paraunit\Configuration\TempFilenameFactory;

class TestWithCoverageCommandLine extends TestCommandLine implements CliCommandInterface
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
    public function __construct(PHPUnitBinFile $phpUnitBin, PHPDbgBinFile $dbgBinFile, TempFilenameFactory $filenameFactory)
    {
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

    public function getOptions(PHPUnitConfig $config, string $uniqueId): string
    {
        $options = '';
        if ($this->phpDbgBinFile->isAvailable()) {
            $options .= '-qrr ' . $this->phpUnitBin->getPhpUnitBin() . ' ';
        }

        return $options
            . parent::getOptions($config, $uniqueId)
            . ' --coverage-php '
            . $this->filenameFactory->getFilenameForCoverage($uniqueId);
    }
}
