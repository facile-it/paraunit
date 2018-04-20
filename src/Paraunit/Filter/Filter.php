<?php

declare(strict_types=1);

namespace Paraunit\Filter;

use Paraunit\Configuration\PHPUnitConfig;
use Paraunit\Proxy\PHPUnitUtilXMLProxy;

/**
 * Class Filter
 * @package Paraunit\Filter
 */
class Filter
{
    /** @var PHPUnitUtilXMLProxy */
    private $utilXml;

    /** @var \File_Iterator_Facade */
    private $fileIteratorFacade;

    /** @var PHPUnitConfig */
    private $configFile;

    /** @var string | null */
    private $relativePath;

    /** @var string | null */
    private $testSuiteFilter;

    /** @var string | null */
    private $stringFilter;

    /**
     * @param PHPUnitUtilXMLProxy $utilXml
     * @param \File_Iterator_Facade $fileIteratorFacade
     * @param PHPUnitConfig $configFile
     * @param string | null $testSuiteFilter
     * @param string | null $stringFilter
     */
    public function __construct(
        PHPUnitUtilXMLProxy $utilXml,
        \File_Iterator_Facade $fileIteratorFacade,
        PHPUnitConfig $configFile,
        string $testSuiteFilter = null,
        string $stringFilter = null
    ) {
        $this->utilXml = $utilXml;
        $this->fileIteratorFacade = $fileIteratorFacade;
        $this->configFile = $configFile;
        $this->relativePath = $configFile->getBaseDirectory() . DIRECTORY_SEPARATOR;
        $this->testSuiteFilter = $testSuiteFilter;
        $this->stringFilter = $stringFilter;
    }

    /**
     * @return string[]
     * @throws \RuntimeException
     */
    public function filterTestFiles(): array
    {
        $aggregatedFiles = [];

        $document = $this->utilXml->loadFile($this->configFile->getFileFullPath());
        $xpath = new \DOMXPath($document);

        /** @var \DOMElement $testSuiteNode */
        foreach ($xpath->query('testsuites/testsuite') as $testSuiteNode) {
            if ($this->testSuitePassFilter($testSuiteNode, $this->testSuiteFilter)) {
                $this->addTestsFromTestSuite($testSuiteNode, $aggregatedFiles);
            }
        }

        return $this->filterByString($aggregatedFiles, $this->stringFilter);
    }

    private function testSuitePassFilter(\DOMElement $testSuiteNode, string $testSuiteFilter = null): bool
    {
        if ($testSuiteFilter === null) {
            return true;
        }

        return \in_array(
            $this->getDOMNodeAttribute($testSuiteNode, 'name'),
            explode(',', $testSuiteFilter),
            true
        );
    }

    /**
     * @param \DOMElement $testSuiteNode
     * @param array $aggregatedFiles
     * @return string[]
     */
    private function addTestsFromTestSuite(\DOMElement $testSuiteNode, array &$aggregatedFiles): array
    {
        $excludes = $this->getExcludesArray($testSuiteNode);

        $this->addTestsFromDirectoryNodes($testSuiteNode, $aggregatedFiles, $excludes);
        $this->addTestsFromFileNodes($testSuiteNode, $aggregatedFiles);

        return $aggregatedFiles;
    }

    /**
     * @param \DOMElement $testSuiteNode
     * @return string[]
     */
    private function getExcludesArray(\DOMElement $testSuiteNode): array
    {
        $excludes = [];
        foreach ($testSuiteNode->getElementsByTagName('exclude') as $excludeNode) {
            $excludes[] = (string) $excludeNode->nodeValue;
        }

        return $excludes;
    }

    /**
     * @param \DOMElement $testSuiteNode
     * @param string[] $aggregatedFiles
     * @param string[] $excludes
     */
    private function addTestsFromDirectoryNodes(\DOMElement $testSuiteNode, array &$aggregatedFiles, array $excludes)
    {
        foreach ($testSuiteNode->getElementsByTagName('directory') as $directoryNode) {
            $directory = (string) $directoryNode->nodeValue;

            $files = $this->fileIteratorFacade->getFilesAsArray(
                $this->relativePath . $directory,
                $this->getDOMNodeAttribute($directoryNode, 'suffix', 'Test.php'),
                $this->getDOMNodeAttribute($directoryNode, 'prefix', ''),
                $excludes
            );

            foreach ($files as $fileName) {
                $this->addFileToAggregateArray($aggregatedFiles, $fileName);
            }
        }
    }

    /**
     * @param \DOMElement $testSuiteNode
     * @param string[] $aggregatedFiles
     */
    private function addTestsFromFileNodes(\DOMElement $testSuiteNode, array &$aggregatedFiles)
    {
        foreach ($testSuiteNode->getElementsByTagName('file') as $fileNode) {
            $fileName = $this->relativePath . (string) $fileNode->nodeValue;
            $this->addFileToAggregateArray($aggregatedFiles, $fileName);
        }
    }

    /**
     * @param array $aggregatedFiles
     * @param string $fileName
     */
    private function addFileToAggregateArray(array &$aggregatedFiles, string $fileName)
    {
        // optimized array_unique
        $aggregatedFiles[$fileName] = $fileName;
    }

    /**
     * @param \DOMElement $testSuiteNode
     * @param string $nodeName
     * @param string|null $defaultValue
     *
     * @return string
     */
    private function getDOMNodeAttribute(
        \DOMElement $testSuiteNode,
        string $nodeName,
        string $defaultValue = null
    ): string {
        /**
         * @var string
         * @var \DOMAttr
         */
        foreach ($testSuiteNode->attributes as $attrName => $attrNode) {
            if ($attrName === $nodeName) {
                return $attrNode->value;
            }
        }

        return $defaultValue;
    }

    /**
     * @param array $aggregatedFiles
     * @param string | null $stringFilter
     * @return string[]
     */
    private function filterByString(array $aggregatedFiles, $stringFilter): array
    {
        if ($stringFilter !== null) {
            $aggregatedFiles = array_filter($aggregatedFiles, function ($value) use ($stringFilter) {
                return stripos($value, $stringFilter) !== false;
            });
        }

        return array_values($aggregatedFiles);
    }
}
