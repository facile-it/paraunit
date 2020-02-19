<?php

declare(strict_types=1);

namespace Paraunit\Configuration;

use Paraunit\File\TempDirectory;
use Paraunit\Parser\JSON\TestHook as Hooks;
use Paraunit\Proxy\PHPUnitUtilXMLProxy;

class PHPUnitConfig
{
    public const DEFAULT_FILE_NAME = 'phpunit.xml';

    public const FALLBACK_CONFIG_FILE_NAME = 'phpunit.xml.dist';

    /** @var TempDirectory */
    private $tempDirectory;

    /** @var PHPUnitUtilXMLProxy */
    private $utilXml;

    /** @var string */
    private $originalFilePath;

    /** @var \DOMXPath|null */
    private $configDOM;

    /** @var PHPUnitOption[] */
    private $phpunitOptions;

    /** @var string|null */
    private $configPath;

    /**
     * @throws \InvalidArgumentException
     */
    public function __construct(
        TempDirectory $tempDirectory,
        PHPUnitUtilXMLProxy $utilXml,
        string $inputPathOrFileName
    ) {
        $this->tempDirectory = $tempDirectory;
        $this->utilXml = $utilXml;
        $this->originalFilePath = $this->getConfigFileRealpath($inputPathOrFileName);
        $this->phpunitOptions = [];
    }

    public function getConfigDOM(): \DOMXPath
    {
        if (null === $this->configDOM) {
            $document = $this->utilXml->loadFile($this->originalFilePath);
            $this->configDOM = $this->createAlteredDOM($document);
        }

        return $this->configDOM;
    }

    /**
     * @return string The full path for the altered, temporary configuration file
     */
    public function getConfigPath(): string
    {
        if (null === $this->configPath) {
            $configPath = $this->tempDirectory->getTempDirForThisExecution() . DIRECTORY_SEPARATOR . self::DEFAULT_FILE_NAME;

            touch($configPath);
            if (false === $this->getConfigDOM()->document->save($configPath)) {
                throw new \RuntimeException('Error while writing temporary PHPUnit configuration');
            }

            $this->configPath = $configPath;
        }

        return $this->configPath;
    }

    /**
     * @return string The relative path from where the configuration defines the testsuites
     */
    public function getBaseDirectory(): string
    {
        return dirname($this->originalFilePath);
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
            $configFile .= DIRECTORY_SEPARATOR . $this->getConfigFile($configFile);
        }

        if (! is_file($configFile) || ! is_readable($configFile)) {
            throw new \InvalidArgumentException('Config file ' . $configFile . ' does not exist or is not readable');
        }

        return $configFile;
    }

    private function getConfigFile(string $path): string
    {
        if (file_exists($path . DIRECTORY_SEPARATOR . self::DEFAULT_FILE_NAME)) {
            return self::DEFAULT_FILE_NAME;
        }

        return self::FALLBACK_CONFIG_FILE_NAME;
    }

    /**
     * @param \DOMDocument $document The DOM of the original configuration
     *
     * @return \DOMXPath The altered configuration, with the TestHooks added
     */
    private function createAlteredDOM(\DOMDocument $document): \DOMXPath
    {
        $config = new \DOMXPath($document);

        $extensionsNode = $document->createElement('extensions');

        $hooks = [
            Hooks\Error::class,
            Hooks\Failure::class,
            Hooks\Incomplete::class,
            Hooks\Risky::class,
            Hooks\Skipped::class,
            Hooks\Successful::class,
            Hooks\Warning::class,
        ];

        foreach ($hooks as $hook) {
            $hookNode = $document->createElement('extension');
            $hookNode->setAttribute('class', $hook);
            $extensionsNode->appendChild($hookNode);
        }

        $config->document->firstChild->appendChild($extensionsNode);

        return $config;
    }
}
