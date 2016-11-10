<?php

namespace Paraunit\Process;

use Paraunit\Configuration\JSONLogFilename;
use Paraunit\Configuration\PHPUnitBinFile;
use Paraunit\Configuration\PHPUnitConfig;

/**
 * Class ProcessFactory
 * @package Paraunit\Process
 */
class ProcessFactory
{
    /** @var  string */
    private $phpUnitBin;

    /** @var  JSONLogFilename */
    private $jsonLogFilename;

    /** @var  PHPUnitConfig */
    private $phpunitConfigFile;

    /**
     * ProcessFactory constructor.
     * @param PHPUnitBinFile $phpUnitBinFile
     */
    public function __construct(PHPUnitBinFile $phpUnitBinFile, JSONLogFilename $jsonLogFilename)
    {
        $this->phpUnitBin = $phpUnitBinFile->getPhpUnitBin();
        $this->jsonLogFilename = $jsonLogFilename;
    }

    /**
     * @param $testFilePath
     * @return SymfonyProcessWrapper
     * @throws \Exception
     */
    public function createProcess($testFilePath)
    {
        if (! $this->phpunitConfigFile instanceof PHPUnitConfig) {
            throw new \Exception('PHPUnit config missing');
        }

        $uniqueId = $this->createUniqueId($testFilePath);
        $command = $this->createCommandLine($testFilePath, $uniqueId);

        return new SymfonyProcessWrapper($command, $uniqueId);
    }

    /**
     * @param PHPUnitConfig $configFile
     */
    public function setConfigFile(PHPUnitConfig $configFile)
    {
        $this->phpunitConfigFile = $configFile;
    }

    /**
     * @param string $testFilePath
     * @param string $uniqueId
     * @return string
     */
    private function createCommandLine($testFilePath, $uniqueId)
    {
        return $this->phpUnitBin .
        ' -c ' . $this->phpunitConfigFile->getFileFullPath() .
        ' --colors=never' .
        ' --log-json=' . $this->jsonLogFilename->generateFromUniqueId($uniqueId) .
        ' ' . $testFilePath;
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
