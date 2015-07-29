<?php

namespace Paraunit\Filter;

use Paraunit\Proxy\PHPUnit_Util_XML_Proxy;
use Symfony\Component\Process\Process;

/**
 * Class Filter
 * @package Paraunit\Filter
 */
class Filter
{
    /** @var  PHPUnit_Util_XML_Proxy */
    protected $utilXml;

    /** @var  \File_Iterator_Facade */
    protected $fileIteratorFacade;

    /**
     * @param PHPUnit_Util_XML_Proxy $utilXml
     * @param \File_Iterator_Facade $fileIteratorFacade
     */
    public function __construct(PHPUnit_Util_XML_Proxy $utilXml, \File_Iterator_Facade $fileIteratorFacade)
    {
        $this->utilXml = $utilXml;
        $this->fileIteratorFacade = $fileIteratorFacade;
    }

    /**
     * @param string $configFile
     * @param string | null $testSuiteFilter
     * @return array
     */
    public function filterTestFiles($configFile, $testSuiteFilter = null)
    {
        $aggregatedFiles = array();

        $document = $this->utilXml->loadFile($configFile, false, true, true);
        $xpath    = new \DOMXPath($document);

        /** @var \DOMNode $testSuiteNode */
        foreach ($xpath->query('testsuites/testsuite') as $testSuiteNode) {

            if (is_null($testSuiteFilter) || $testSuiteFilter == $this->getTestSuiteName($testSuiteNode)) {

                foreach ($testSuiteNode->getElementsByTagName('directory') as $directoryNode) {

                    $directory = (string) $directoryNode->nodeValue;

                    $files = $this->fileIteratorFacade->getFilesAsArray(
                        $directory,
                        'Test.php',
                        '',
                        array()
                    );

                    $aggregatedFiles = array_merge($aggregatedFiles,$files);
                }
            }
        }

        return $aggregatedFiles;
    }

    /**
     * @param \DOMNode $testSuiteNode
     * @return string
     */
    private function getTestSuiteName(\DOMNode $testSuiteNode)
    {
        /**
         * @var string $attrName
         * @var \DOMAttr $attrNode
         */
        foreach ($testSuiteNode->attributes as $attrName => $attrNode) {
            if ($attrName == 'name') {
                return $attrNode->value;
            }
        }

        return '';
    }
}
