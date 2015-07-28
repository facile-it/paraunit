<?php

namespace Paraunit\Filter;

use Symfony\Component\Process\Process;

/**
 * Class Filter
 * @package Paraunit\Filter
 */
class Filter
{
    /**
     * @param string $configFile
     * @param $testSuiteFilter
     * @return array
     * @internal param $testsuite
     */
    public function filterTestFiles($configFile, $testSuiteFilter)
    {

        $aggregatedFiles = array();

        $document = \PHPUnit_Util_XML::loadFile($configFile, false, true, true);
        $xpath    = new \DOMXPath($document);

        $fileIteratorFacade = new \File_Iterator_Facade;

        foreach ($xpath->query('testsuites/testsuite') as $testSuiteNode){

            foreach ($testSuiteNode->getElementsByTagName('directory') as $directoryNode) {

                $directory = (string) $directoryNode->nodeValue;

                $files = $fileIteratorFacade->getFilesAsArray(
                    $directory,
                    'Test.php',
                    '',
                    array()
                );

                $aggregatedFiles = array_merge($aggregatedFiles,$files);

            }
        }

        return $aggregatedFiles;
    }

}
