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
     * @param $testsuite
     * @return array
     */
    public function filterTestFiles($configFile, $testsuite)
    {
        $files = array();

        $iterator = \PHPUnit_Util_Configuration
            ::getInstance($configFile)
            ->getTestSuiteConfiguration($testsuite)
            ->getIterator();

        /** @var \PHPUnit_Framework_TestSuite $testSuite */
        foreach ($iterator as $testSuite) {
            $tests = $testSuite->getIterator();

            foreach ($tests as $test) {
                $class = new \ReflectionClass($test);
                $files[] = $class->getFileName();
            }
        }

        return $files;

    }
}
