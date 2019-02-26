<?php

declare(strict_types=1);

namespace Paraunit\Configuration;

class PHPUnitConfig
{
    public const DEFAULT_FILE_NAME = 'phpunit.xml';

    public const FALLBACK_CONFIG_FILE_NAME = 'phpunit.xml.dist';

    /** @var string */
    private $configFilename;

    /** @var PHPUnitOption[] */
    private $phpunitOptions;

    /**
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

    public function addPhpunitOption(PHPUnitOption $option): void
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
     * @throws \InvalidArgumentException
     */
    private function getConfigFileRealpath(string $inputPathOrFileName): string
    {
        $configFile = realpath($inputPathOrFileName);

        if (false === $configFile) {
            throw new \InvalidArgumentException('Config path/file provided is not valid: ' . $inputPathOrFileName);
        }

        if (is_dir($configFile)) {
            $configFileFallback = $configFile . DIRECTORY_SEPARATOR . self::FALLBACK_CONFIG_FILE_NAME;
            $configFile .= DIRECTORY_SEPARATOR . self::DEFAULT_FILE_NAME;

            if (! file_exists($configFile)) {
                $configFile = $configFileFallback;
            }
        }

        if (! is_file($configFile) || ! is_readable($configFile)) {
            throw new \InvalidArgumentException('Config file ' . $configFile . ' does not exist or is not readable');
        }

        return $configFile;
    }
}
