<?php

namespace Paraunit\Process;

use Paraunit\Configuration\PHPUnitBinFile;
use Paraunit\Configuration\PHPUnitConfigFile;

/**
 * Class ProcessFactory
 * @package Paraunit\Process
 */
class ProcessFactory
{
    /** @var  string */
    private $phpUnitBin;

    /** @var  PHPUnitConfigFile */
    private $phpunitConfigFile;

    /**
     * ProcessFactory constructor.
     * @param PHPUnitBinFile $phpUnitBinFile
     */
    public function __construct(PHPUnitBinFile $phpUnitBinFile)
    {
        $this->phpUnitBin = $phpUnitBinFile->getPhpUnitBin();
    }

    /**
     * @param $testFilePath
     * @return SymfonyProcessWrapper
     * @throws \Exception
     */
    public function createProcess($testFilePath)
    {
        if ( ! $this->phpunitConfigFile instanceof PHPUnitConfigFile) {
            throw new \Exception('PHPUnit config missing');
        }

        $command = $this->createCommandLine($testFilePath);

        return new SymfonyProcessWrapper($command);
    }

    /**
     * @param PHPUnitConfigFile $configFile
     */
    public function setConfigFile(PHPUnitConfigFile $configFile)
    {
        $this->phpunitConfigFile = $configFile;
    }

    /**
     * @param $testFilePath
     * @return string
     *
     * @todo Separate with appends and prepends, maybe in multiple dedicate classes;
     *       we will need it for PHP7 code coverage with PHPDbg
     */
    private function createCommandLine($testFilePath)
    {
        return $this->phpUnitBin .
        ' -c ' . $this->phpunitConfigFile->getFileFullPath() .
        ' --colors=never ' .
        $testFilePath .
        ' 2>&1';
    }
}
