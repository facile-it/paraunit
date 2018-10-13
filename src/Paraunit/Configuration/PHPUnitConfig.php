<?php

declare(strict_types=1);

namespace Paraunit\Configuration;

/**
 * Class PHPUnitConfig
 */
class PHPUnitConfig
{
    const DEFAULT_FILE_NAME = 'phpunit.xml.dist';

    /** @var string */
    private $configFilename;

    /** @var PHPUnitOption[] */
    private $phpunitOptions;

    /**
     * @param string $inputPathOrFileName
     *
     * @throws \InvalidArgumentException
     */
    public function __construct(string $inputPathOrFileName)
    {
        $this->configFilename = $this->getConfigFileRealpath($inputPathOrFileName);
        $this->phpunitOptions = [];
    }

    /**
     * @return string The full path for this configuration file
     */
    public function getFileFullPath(): string
    {
        return $this->configFilename;
    }

    /**
     * @return string The relative path from where the configuration defines the testsuites
     */
    public function getBaseDirectory(): string
    {
        return dirname($this->configFilename);
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
    public function getPhpunitOptions(): array
    {
        return $this->phpunitOptions;
    }

    /**
     * @param string $inputPathOrFileName
     *
     * @throws \InvalidArgumentException
     *
     * @return string
     */
    private function getConfigFileRealpath(string $inputPathOrFileName): string
    {
        $configFile = realpath($inputPathOrFileName);

        if (false === $configFile) {
            throw new \InvalidArgumentException('Config path/file provided is not valid: ' . $inputPathOrFileName);
        }

        if (is_dir($configFile)) {
            $configFile .= DIRECTORY_SEPARATOR . self::DEFAULT_FILE_NAME;
        }

        if (! is_file($configFile) || ! is_readable($configFile)) {
            throw new \InvalidArgumentException('Config file ' . $configFile . ' does not exist or is not readable');
        }

        return $configFile;
    }
}
