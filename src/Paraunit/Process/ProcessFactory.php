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

        $uniqueId = $this->createUniqueId($testFilePath);
        $command = $this->createCommandLine($testFilePath, $uniqueId);

        return new SymfonyProcessWrapper($command, $uniqueId);
    }

    /**
     * @param PHPUnitConfigFile $configFile
     */
    public function setConfigFile(PHPUnitConfigFile $configFile)
    {
        $this->phpunitConfigFile = $configFile;
    }

    /**
     * @param string $testFilePath
     * @param string $uniqueId
     * @return string
     *
     * @todo Separate with appends and prepends, maybe in multiple dedicate classes;
     *       we will need it for PHP7 code coverage with PHPDbg
     */
    private function createCommandLine($testFilePath, $uniqueId)
    {
        return $this->phpUnitBin .
        ' -c ' . $this->phpunitConfigFile->getFileFullPath() .
        ' --colors=never' .
        ' --log-json=/dev/shm/paraunit/logs/' . $uniqueId . '.json.log' .
        ' ' . $testFilePath .
        ' 2>&1';
    }

    /**
     * @param string $testFilePath
     * @return string
     */
    private function createUniqueId($testFilePath)
    {
        return md5($testFilePath);
    }
}
