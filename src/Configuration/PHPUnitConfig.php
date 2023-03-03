<?php

declare(strict_types=1);

namespace Paraunit\Configuration;

use PHPUnit\Util\Xml\Loader;

class PHPUnitConfig
{
    final public const DEFAULT_FILE_NAME = 'phpunit.xml';

    final public const FALLBACK_CONFIG_FILE_NAME = 'phpunit.xml.dist';

    private readonly string $configFilename;

    private readonly Loader $xmlLoader;

    /**
     * @throws \InvalidArgumentException
     */
    public function __construct(string $inputPathOrFileName)
    {
        $this->configFilename = $this->getConfigFileRealpath($inputPathOrFileName);
        /** @psalm-suppress InternalClass */
        $this->xmlLoader = new Loader();
    }

    public function isParaunitExtensionRegistered(): bool
    {
        /** @psalm-suppress InternalMethod */
        $config = $this->xmlLoader->loadFile($this->configFilename);
        $xpath = new \DOMXPath($config);
        $extensions = $xpath->query('extensions/bootstrap');

        if (! $extensions instanceof \DOMNodeList) {
            return false;
        }

        $extensionName = ParaunitExtension::class;
        foreach ($extensions as $extension) {
            if (! $extension instanceof \DOMElement) {
                continue;
            }

            $class = $extension->attributes?->getNamedItem('class');
            if (! $class instanceof \DOMAttr) {
                continue;
            }

            $className = ltrim($class->value, '\\');

            if ($className === $extensionName) {
                return true;
            }
        }

        return false;
    }

    public function installExtension(): void
    {
        if ($this->isParaunitExtensionRegistered()) {
            return;
        }

        /** @psalm-suppress InternalMethod */
        $config = $this->xmlLoader->loadFile($this->configFilename);
        $config->preserveWhiteSpace = false;
        $config->formatOutput = true;

        $extensionsNode = $this->getExtensionsNode($config);
        $paraunitExtension = $config->createElement('bootstrap');
        $paraunitExtension->setAttribute('class', ParaunitExtension::class);
        $extensionsNode->prepend($paraunitExtension);

        $config->save($this->configFilename);
    }

    private function getExtensionsNode(\DOMDocument $config): \DOMElement
    {
        $nodes = iterator_to_array($config->getElementsByTagName('phpunit')->getIterator());
        $phpunitNode = array_pop($nodes);

        if (! $phpunitNode instanceof \DOMElement) {
            throw new \InvalidArgumentException('PHPUnit configuration is malformed - unable to install ParaunitExtension automatically');
        }

        foreach ($phpunitNode->childNodes->getIterator() as $childNode) {
            if ($childNode instanceof \DOMElement && $childNode->nodeName === 'extensions') {
                return $childNode;
            }
        }

        $extensionsNode = $config->createElement('extensions');
        $phpunitNode->append($extensionsNode);

        return $extensionsNode;
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
}
