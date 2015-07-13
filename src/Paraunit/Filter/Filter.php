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
     * @param        $testsuite
     * @return array
     */
    public function filterTestFiles($configFile, $testsuite)
    {
        $files = array();

        $iterator = \PHPUnit_Util_Configuration
            ::getInstance($configFile)
            ->getTestSuiteConfiguration($testsuite)
            ->getIterator();

        foreach ($iterator as $testSuite) {
            $files = array_merge($files + $this->extractTests($testSuite));
        }

        $files =  array_unique($files);

        return $files;
    }

    /**
     * @param \PHPUnit_Framework_TestSuite $testSuite
     * @return string[]
     */
    protected function extractTests(\PHPUnit_Framework_TestSuite $testSuite)
    {
        $files = array();

        foreach($testSuite->tests() as $t) {
            if ($t instanceof \PHPUnit_Framework_TestSuite) {
                // WARNING -- recursive function
                $files = array_merge($files, $this->extractTests($t));
            } else {
                $class   = new \ReflectionClass($t);
                $files[] = $class->getFileName();
            }

        }

        return $files;
    }
}
