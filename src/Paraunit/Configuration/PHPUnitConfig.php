<?php

namespace Paraunit\Configuration;

/**
 * Class PHPUnitConfig
 * @package Paraunit\Configuration
 */
class PHPUnitConfig
{
    const DEFAULT_FILE_NAME = 'phpunit.xml.dist';
    const COPY_FILE_NAME = 'phpunit-paraunit.xml';

    /** @var TempFilenameFactory */
    private $tempFilenameFactory;

    /** @var string */
    private $configFile;

    /** @var string */
    private $originalFilename;

    /** @var PHPUnitOption[] */
    private $phpunitOptions;

    /**
     * @param TempFilenameFactory $tempFilenameFactory
     * @param string $inputPathOrFileName
     * @throws \InvalidArgumentException
     */
    public function __construct(TempFilenameFactory $tempFilenameFactory, string $inputPathOrFileName)
    {
        $this->tempFilenameFactory = $tempFilenameFactory;
        $this->originalFilename = $this->getConfigFileRealpath($inputPathOrFileName);
        $this->phpunitOptions = [];
    }

    /**
     * Get the full path for this configuration file
     * @return string
     * @throws \RuntimeException
     */
    public function getFileFullPath(): string
    {
        if (null === $this->configFile) {
            $this->configFile = $this->copyAndAlterConfig($this->originalFilename);
        }

        return $this->configFile;
    }

    /**
     * The relative path from where the configuration defines the testsuites
     * @return string
     */
    public function getBaseDirectory(): string
    {
        return dirname($this->originalFilename);
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
     * @return string
     * @throws \InvalidArgumentException
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

    /**
     * @param string $originalConfigFilename
     * @return string The full filename of the new temp config
     * @throws \RuntimeException
     */
    private function copyAndAlterConfig(string $originalConfigFilename): string
    {
        $originalConfig = file_get_contents($originalConfigFilename);
        $document = new \DOMDocument;
        $document->preserveWhiteSpace = false;

        $document->loadXML($originalConfig);
        $this->alterBoostrap($document);
        $this->appendLogListener($document);

        $newFilename = dirname($this->originalFilename) . DIRECTORY_SEPARATOR . 'phpunit-paraunit.xml';

        if (false === file_put_contents($newFilename, $document->saveXML())) {
            throw new \RuntimeException('Error while saving temporary config in ' . $newFilename);
        }

        return $newFilename;
    }

    /**
     * @param string $originalBoostrap
     * @return bool
     */
    private function isRelativePath($originalBoostrap): bool
    {
        return 0 === preg_match('~(^[A-Z]:)|(^/)~', $originalBoostrap);
    }

    private function alterBoostrap(\DOMDocument $document)
    {
        $rootNode = $document->documentElement;

        $originalBoostrap = $rootNode->getAttribute('bootstrap');
        if ($originalBoostrap && $this->isRelativePath($originalBoostrap)) {
            $newBootstrapPath = $this->getBaseDirectory() . DIRECTORY_SEPARATOR . $originalBoostrap;
            $rootNode->setAttribute('bootstrap', $newBootstrapPath);
        }
    }

    private function appendLogListener(\DOMDocument $document)
    {
        $rootNode = $document->documentElement;

        $textNode = $document->createTextNode($this->tempFilenameFactory->getPathForLog());
        $logDirNode = $document->createElement('string');
        $logDirNode->appendChild($textNode);
        $argumentsNode = $document->createElement('arguments');
        $argumentsNode->appendChild($logDirNode);
        $logListenerNode = $document->createElement('listener');
        $logListenerNode->setAttribute('class', StaticOutputPath::class);
        $logListenerNode->appendChild($argumentsNode);

        $listenersNode = $rootNode->getElementsByTagName('listeners')->item(0);
        if (! $listenersNode) {
            $listenersNode = $document->createElement('listeners');
            $rootNode->appendChild($listenersNode);
        }

        $listenersNode->appendChild($logListenerNode);
    }
}
