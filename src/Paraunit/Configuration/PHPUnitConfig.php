<?php

namespace Paraunit\Configuration;

/**
 * Class PHPUnitConfig
 * @package Paraunit\Configuration
 */
class PHPUnitConfig
{
    const DEFAULT_FILE_NAME = 'phpunit.xml.dist';

    /** @var  TempFilenameFactory */
    private $tempFilenameFactory;

    /** @var string */
    private $configFile;

    /** @var string */
    private $baseDirectory;

    /** @var  PHPUnitOption[] */
    private $phpunitOptions;

    /**
     * @param TempFilenameFactory $tempFilenameFactory
     * @param string $inputPathOrFileName
     * @throws \InvalidArgumentException
     */
    public function __construct(TempFilenameFactory $tempFilenameFactory, $inputPathOrFileName)
    {
        $this->phpunitOptions = array();
        $this->tempFilenameFactory = $tempFilenameFactory;

        $originalConfigFilename = $this->getConfigFileRealpath($inputPathOrFileName);
        $this->configFile = $this->copyAndAlterConfig($originalConfigFilename);
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
     * The relative path from where the configuration defines the testsuites
     * @return string
     */
    public function getBaseDirectory()
    {
        return $this->baseDirectory;
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

    /**
     * @param string $inputPathOrFileName
     * @return string
     * @throws \InvalidArgumentException
     */
    private function getConfigFileRealpath($inputPathOrFileName)
    {
        $inputPathOrFileName = realpath($inputPathOrFileName);

        if (false === $inputPathOrFileName) {
            throw new \InvalidArgumentException('Config path/file provided is not valid (does it exist?)');
        }

        $configFile = $inputPathOrFileName;

        if (is_dir($configFile)) {
            $this->baseDirectory = dirname($configFile);
            $configFile .= DIRECTORY_SEPARATOR . self::DEFAULT_FILE_NAME;
        }

        if (! is_file($configFile) || ! is_readable($configFile)) {
            throw new \InvalidArgumentException('Config file ' . $configFile . ' does not exist or is not readable');
        }

        return $configFile;
    }

    private function copyAndAlterConfig($originalConfigFilename)
    {
        $originalConfig = file_get_contents($originalConfigFilename);
        $document = new \DOMDocument;
        $document->preserveWhiteSpace = false;

        $document->loadXML($originalConfig);
        $rootNode = $document->documentElement;
        $rootNode->setAttribute('printerFile', $this->tempFilenameFactory->getPathForLog());
        $rootNode->setAttribute('printerClass', 'Paraunit\Parser\JSON\LogPrinter');

        $newFilename = $this->tempFilenameFactory->getFilenameForConfiguration();
        file_put_contents($newFilename, $document->saveXML());

        return $newFilename;
    }
}
