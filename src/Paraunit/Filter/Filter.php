<?php

namespace Paraunit\Filter;

use Paraunit\Configuration\PHPUnitConfigFile;
use Paraunit\Proxy\PHPUnitUtilXMLProxy;

/**
 * Class Filter
 * @package Paraunit\Filter
 */
class Filter
{
    /** @var  PHPUnitUtilXMLProxy */
    protected $utilXml;

    /** @var  \File_Iterator_Facade */
    protected $fileIteratorFacade;

    /** @var  string | null */
    protected $relativePath;

    /**
     * @param PHPUnitUtilXMLProxy $utilXml
     * @param \File_Iterator_Facade $fileIteratorFacade
     */
    public function __construct(PHPUnitUtilXMLProxy $utilXml, \File_Iterator_Facade $fileIteratorFacade)
    {
        $this->utilXml = $utilXml;
        $this->fileIteratorFacade = $fileIteratorFacade;
    }

    /**
     * @param PHPUnitConfigFile $configFile
     * @param string | null $testSuiteFilter
     *
     * @return array
     */
    public function filterTestFiles(PHPUnitConfigFile $configFile, $testSuiteFilter = null)
    {
        $aggregatedFiles = array();
        $this->relativePath = $configFile->getDirectory() . DIRECTORY_SEPARATOR;

        $document = $this->utilXml->loadFile($configFile->getFileFullPath(), false, true, true);
        $xpath = new \DOMXPath($document);

        /** @var \DOMNode $testSuiteNode */
        foreach ($xpath->query('testsuites/testsuite') as $testSuiteNode) {
            if (is_null($testSuiteFilter) || $testSuiteFilter == $this->getDOMNodeAttribute($testSuiteNode, 'name')) {
                $this->addTestsFromTestSuite($testSuiteNode, $aggregatedFiles);
            }
        }

        return array_values($aggregatedFiles);
    }

    /**
     * @param \DOMNode $testSuiteNode
     * @param array $aggregatedFiles
     * @return array|\string[]
     */
    private function addTestsFromTestSuite(\DOMNode $testSuiteNode, array &$aggregatedFiles)
    {
        $excludes = $this->getExcludesArray($testSuiteNode);

        $this->addTestsFromDirectoryNodes($testSuiteNode, $aggregatedFiles, $excludes);
        $this->addTestsFromFileNodes($testSuiteNode, $aggregatedFiles);

        return $aggregatedFiles;
    }

    /**
     * @param \DOMNode $testSuiteNode
     * @return array
     */
    private function getExcludesArray(\DOMNode $testSuiteNode)
    {
        $excludes = array();
        foreach ($testSuiteNode->getElementsByTagName('exclude') as $excludeNode) {
            $excludes[] = (string)$excludeNode->nodeValue;
        }

        return $excludes;
    }

    /**
     * @param \DOMNode $testSuiteNode
     * @param array $aggregatedFiles
     * @param array $excludes
     */
    private function addTestsFromDirectoryNodes(\DOMNode $testSuiteNode, array &$aggregatedFiles, array $excludes)
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
     * @param \DOMNode $testSuiteNode
     * @param array $aggregatedFiles
     */
    private function addTestsFromFileNodes(\DOMNode $testSuiteNode, array &$aggregatedFiles)
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
     * @param \DOMNode $testSuiteNode
     * @param string $nodeName
     * @param string $defaultValue
     *
     * @return string
     */
    private function getDOMNodeAttribute(\DOMNode $testSuiteNode, $nodeName, $defaultValue = null)
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
}
