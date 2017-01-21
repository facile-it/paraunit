<?php

namespace Paraunit\Filter;

use Paraunit\Configuration\PHPUnitConfig;
use Paraunit\Proxy\PHPUnitUtilXMLProxy;

/**
 * Class Filter
 * @package Paraunit\Filter
 */
class Filter
{
    /** @var  PHPUnitUtilXMLProxy */
    private $utilXml;

    /** @var  \File_Iterator_Facade */
    private $fileIteratorFacade;

    /** @var PHPUnitConfig */
    private $configFile;

    /** @var  string | null */
    private $relativePath;

    /** @var null */
    private $testSuiteFilter;

    /** @var null */
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
        $testSuiteFilter = null,
        $stringFilter = null
    ) {
        $this->utilXml = $utilXml;
        $this->fileIteratorFacade = $fileIteratorFacade;
        $this->configFile = $configFile;
        $this->relativePath = $configFile->getBaseDirectory() . DIRECTORY_SEPARATOR;
        $this->testSuiteFilter = $testSuiteFilter;
        $this->stringFilter = $stringFilter;
    }

    /**
     * @return array
     */
    public function filterTestFiles()
    {
        $aggregatedFiles = array();

        $document = $this->utilXml->loadFile($this->configFile->getFileFullPath(), false, true, true);
        $xpath = new \DOMXPath($document);

        /** @var \DOMNode $testSuiteNode */
        foreach ($xpath->query('testsuites/testsuite') as $testSuiteNode) {
            if (null === $this->testSuiteFilter || $this->testSuiteFilter === $this->getDOMNodeAttribute($testSuiteNode, 'name')) {
                $this->addTestsFromTestSuite($testSuiteNode, $aggregatedFiles);
            }
        }

        return $this->filterByString($aggregatedFiles, $this->stringFilter);
    }

    /**
     * @param \DOMElement $testSuiteNode
     * @param array $aggregatedFiles
     * @return array|\string[]
     */
    private function addTestsFromTestSuite(\DOMElement $testSuiteNode, array &$aggregatedFiles)
    {
        $excludes = $this->getExcludesArray($testSuiteNode);

        $this->addTestsFromDirectoryNodes($testSuiteNode, $aggregatedFiles, $excludes);
        $this->addTestsFromFileNodes($testSuiteNode, $aggregatedFiles);

        return $aggregatedFiles;
    }

    /**
     * @param \DOMElement $testSuiteNode
     * @return array
     */
    private function getExcludesArray(\DOMElement $testSuiteNode)
    {
        $excludes = array();
        foreach ($testSuiteNode->getElementsByTagName('exclude') as $excludeNode) {
            $excludes[] = (string)$excludeNode->nodeValue;
        }

        return $excludes;
    }

    /**
     * @param \DOMElement $testSuiteNode
     * @param array $aggregatedFiles
     * @param array $excludes
     */
    private function addTestsFromDirectoryNodes(\DOMElement $testSuiteNode, array &$aggregatedFiles, array $excludes)
    {
        foreach ($testSuiteNode->getElementsByTagName('directory') as $directoryNode) {
            $directory = (string)$directoryNode->nodeValue;

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
     * @param array $aggregatedFiles
     */
    private function addTestsFromFileNodes(\DOMElement $testSuiteNode, array &$aggregatedFiles)
    {
        foreach ($testSuiteNode->getElementsByTagName('file') as $fileNode) {
            $fileName = $this->relativePath . (string)$fileNode->nodeValue;
            $this->addFileToAggregateArray($aggregatedFiles, $fileName);
        }
    }

    /**
     * @param array $aggregatedFiles
     * @param string $fileName
     */
    private function addFileToAggregateArray(array &$aggregatedFiles, $fileName)
    {
        // optimized array_unique
        $aggregatedFiles[$fileName] = $fileName;
    }

    /**
     * @param \DOMElement $testSuiteNode
     * @param string $nodeName
     * @param string $defaultValue
     *
     * @return string
     */
    private function getDOMNodeAttribute(\DOMElement $testSuiteNode, $nodeName, $defaultValue = null)
    {
        /**
         * @var string
         * @var \DOMAttr
         */
        foreach ($testSuiteNode->attributes as $attrName => $attrNode) {
            if ($attrName == $nodeName) {
                return $attrNode->value;
            }
        }

        return $defaultValue;
    }

    /**
     * @param array $aggregatedFiles
     * @param string | null $stringFilter
     * @return array
     */
    private function filterByString(array $aggregatedFiles, $stringFilter)
    {
        if ($stringFilter !== null) {
            $aggregatedFiles = array_filter($aggregatedFiles, function ($value) use ($stringFilter) {
                return stripos($value, $stringFilter) !== false;
            });
        }

        return array_values($aggregatedFiles);
    }
}
