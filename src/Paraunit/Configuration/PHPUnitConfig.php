<?php

namespace Paraunit\Configuration;

/**
 * Class PHPUnitConfig
 * @package Paraunit\Configuration
 */
class PHPUnitConfig
{
    const DEFAULT_FILE_NAME = 'phpunit.xml.dist';

    /** @var string */
    private $configFile;
    
    /** @var  PHPUnitOption[] */
    private $phpunitOptions;

    /**
     * @param string $inputPathOrFileName
     * @throws \InvalidArgumentException
     */
    public function __construct($inputPathOrFileName)
    {
        $inputPathOrFileName = realpath($inputPathOrFileName);

        if (false === $inputPathOrFileName) {
            throw new \InvalidArgumentException('Config path/file provided is not valid (does it exist?)');
        }

        $configFile = is_dir($inputPathOrFileName)
            ? $inputPathOrFileName . DIRECTORY_SEPARATOR . self::DEFAULT_FILE_NAME
            : $inputPathOrFileName;

        if (! is_file($configFile) || ! is_readable($configFile)) {
            throw new \InvalidArgumentException('Config file ' . $configFile . ' does not exist or is not readable');
        }

        $this->configFile = $configFile;
        $this->phpunitOptions = array();
    }

    /**
     * Get the full path for this configuration file
     * @return string
     */
    public function getFileFullPath()
    {
        return $this->configFile;
    }

    /**
     * Get the directory which contains this configuration file
     * @return string
     */
    public function getDirectory()
    {
        return dirname($this->configFile);
    }

    /**
     * @param PHPUnitOption $option
     */
    public function addPhpunitOption(PHPUnitOption $option)
    {
        $this->phpunitOptions[] = $option;
    }

    /**
     * @return PHPUnitOption[]
     */
    public function getPhpunitOptions()
    {
        return $this->phpunitOptions;
    }
}
