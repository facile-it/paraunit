<?php

namespace Paraunit\Process;


use Paraunit\Configuration\PHPDbgBinFile;
use Paraunit\Configuration\PHPUnitBinFile;
use Paraunit\Configuration\PHPUnitConfigFile;
use Paraunit\Configuration\TempFilenameFactory;

class TestWithCoverageCommandLine extends TestCommandLine implements CliCommandInterface
{
    /** @var PHPDbgBinFile */
    private $phpDbgBinFile;

    /**
     * TestCliCommand constructor.
     * @param PHPUnitBinFile $phpUnitBin
     * @param PHPDbgBinFile $dbgBinFile
     * @param TempFilenameFactory $filenameFactory
     */
    public function __construct(PHPUnitBinFile $phpUnitBin, PHPDbgBinFile $dbgBinFile, TempFilenameFactory $filenameFactory)
    {
        parent::__construct($phpUnitBin, $filenameFactory);

        $this->phpDbgBinFile = $dbgBinFile;
    }

    /**
     * @return string
     */
    public function getExecutable()
    {
        if ($this->phpDbgBinFile->isAvailable()) {
            return $this->phpDbgBinFile->getPhpDbgBin();
        }

        return parent::getExecutable();
    }

    /**
     * @param PHPUnitConfigFile $configFile
     * @param string $uniqueId
     * @return string
     */
    public function getOptions(PHPUnitConfigFile $configFile, $uniqueId)
    {
        $options = '';
        if ($this->phpDbgBinFile->isAvailable()) {
            $options .= '-qrr '
                . parent::getExecutable() . ' ';
        }

        return $options
            . parent::getOptions($configFile, $uniqueId)
            . ' --coverage-php '
            . $this->filenameFactory->getFilenameForCoverage($uniqueId);
    }
}
