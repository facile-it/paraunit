<?php

namespace Paraunit\Filter;

use Paraunit\Proxy\PHPUnit_Util_XML_Proxy;

/**
 * Class Filter.
 */
class Filter
{
    /** @var  PHPUnit_Util_XML_Proxy */
    protected $utilXml;

    /** @var  \File_Iterator_Facade */
    protected $fileIteratorFacade;

    /**
     * @param PHPUnit_Util_XML_Proxy $utilXml
     * @param \File_Iterator_Facade  $fileIteratorFacade
     */
    public function __construct(PHPUnit_Util_XML_Proxy $utilXml, \File_Iterator_Facade $fileIteratorFacade)
    {
        $this->utilXml = $utilXml;
        $this->fileIteratorFacade = $fileIteratorFacade;
    }

    /**
     * @param string        $configFile
     * @param string | null $testSuiteFilter
     *
     * @return array
     */
    public function filterTestFiles($configFile, $testSuiteFilter = null)
    {
        $aggregatedFiles = array();

        $document = $this->utilXml->loadFile($configFile, false, true, true);
        $xpath = new \DOMXPath($document);

        /** @var \DOMNode $testSuiteNode */
        foreach ($xpath->query('testsuites/testsuite') as $testSuiteNode) {
            if (is_null($testSuiteFilter) || $testSuiteFilter == $this->getDOMNodeAttribute($testSuiteNode, 'name')) {
                // optimized array_unique
                foreach ($this->extractFileFromTestSuite($testSuiteNode) as $file) {
                    $aggregatedFiles[$file] = $file;
                }
            }
        }

        return array_values($aggregatedFiles);
    }

    /**
     * @param \DOMNode $testSuiteNode
     *
     * @return array | string[]
     */
    private function extractFileFromTestSuite(\DOMNode $testSuiteNode)
    {
        $aggregatedFiles = array();

        foreach ($testSuiteNode->getElementsByTagName('directory') as $directoryNode) {
            $directory = (string) $directoryNode->nodeValue;

            $files = $this->fileIteratorFacade->getFilesAsArray(
                $directory,
                $this->getDOMNodeAttribute($directoryNode, 'suffix', 'Test.php'),
                $this->getDOMNodeAttribute($directoryNode, 'prefix'),
                array()
            );

            // optimized array_unique
            foreach ($files as $file) {
                $aggregatedFiles[$file] = $file;
            }
        }

        return $aggregatedFiles;
    }

    /**
     * @param \DOMNode      $testSuiteNode
     * @param string        $nodeName
     * @param string | null $defaultValue
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
