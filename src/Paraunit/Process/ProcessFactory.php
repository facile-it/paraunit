<?php

namespace Paraunit\Process;

use Paraunit\Configuration\JSONLogFilename;
use Paraunit\Configuration\PHPDbgBinFile;
use Paraunit\Configuration\PHPUnitBinFile;
use Paraunit\Configuration\PHPUnitConfigFile;

/**
 * Class ProcessFactory
 * @package Paraunit\Process
 */
class ProcessFactory
{
    /** @var  PHPUnitBinFile */
    private $phpUnitBin;

    /** @var  PHPDbgBinFile */
    private $phpDbgFile;

    /** @var  JSONLogFilename */
    private $jsonLogFilename;

    /** @var  PHPUnitConfigFile */
    private $phpunitConfigFile;

    /**
     * ProcessFactory constructor.
     * @param PHPUnitBinFile $phpUnitBin
     * @param PHPDbgBinFile $phpDbgFile
     * @param JSONLogFilename $jsonLogFilename
     */
    public function __construct(PHPUnitBinFile $phpUnitBin, PHPDbgBinFile $phpDbgFile, JSONLogFilename $jsonLogFilename)
    {
        $this->phpUnitBin = $phpUnitBin;
        $this->phpDbgFile = $phpDbgFile;
        $this->jsonLogFilename = $jsonLogFilename;
    }

    /**
     * @param $testFilePath
     * @return SymfonyProcessWrapper
     * @throws \Exception
     */
    public function createProcess($testFilePath)
    {
        if (! $this->phpunitConfigFile instanceof PHPUnitConfigFile) {
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
     */
    private function createCommandLine($testFilePath, $uniqueId)
    {
        $commandLine = '';
        if ($this->phpDbgFile->isAvailable()) {
            $commandLine .= $this->phpDbgFile->getPhpDbgBin() . ' -qrr ';
        }

        $commandLine .= $this->phpUnitBin->getPhpUnitBin() .
            ' -c ' . $this->phpunitConfigFile->getFileFullPath() .
            ' --colors=never' .
            ' --log-json=' . $this->jsonLogFilename->generateFromUniqueId($uniqueId) .
            ' ' . $testFilePath;

        return $commandLine;
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
