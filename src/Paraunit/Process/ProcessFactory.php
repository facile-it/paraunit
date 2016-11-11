<?php

namespace Paraunit\Process;

use Paraunit\Configuration\JSONLogFilename;
use Paraunit\Configuration\PHPUnitBinFile;
use Paraunit\Configuration\PHPUnitConfig;
use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;

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
    private $phpunitConfig;

    /**
     * ProcessFactory constructor.
     * @param PHPUnitBinFile $phpUnitBinFile
     * @param JSONLogFilename $jsonLogFilename
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
        if (! $this->phpunitConfig instanceof PHPUnitConfig) {
            throw new InvalidConfigurationException('PHPUnit config missing');
        }

        $uniqueId = $this->createUniqueId($testFilePath);
        $command = $this->createCommandLine($testFilePath, $uniqueId);

        return new SymfonyProcessWrapper($command, $uniqueId);
    }

    /**
     * @param PHPUnitConfig $configFile
     */
    public function setConfig(PHPUnitConfig $configFile)
    {
        $this->phpunitConfig = $configFile;
    }

    /**
     * @param string $testFilePath
     * @param string $uniqueId
     * @return string
     */
    private function createCommandLine($testFilePath, $uniqueId)
    {
        $commandLine = $this->phpUnitBin
            . ' --configuration=' . $this->phpunitConfig->getFileFullPath()
            . ' --log-json=' . $this->jsonLogFilename->generateFromUniqueId($uniqueId);

        foreach ($this->phpunitConfig->getPhpunitOptions() as $option) {
            $commandLine .= ' --' . $option->getName();
            if ($option->hasValue()) {
                $commandLine .= '=' . $option->getValue();
            }
        }

        return $commandLine . ' ' . $testFilePath;
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
