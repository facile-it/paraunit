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


        /** @var \PHPUnit_Framework_TestSuite $testSuite */
        foreach ($iterator as $testSuite) {

            $tests = $testSuite->getIterator();

            foreach ($tests as $test) {

                if ($test instanceof \PHPUnit_Framework_TestSuite_DataProvider) {

                    $actualTests = $test->tests();
                    foreach ($actualTests as $actualTest) {
                        $class   = new \ReflectionClass($actualTest);
                        $files[] = $class->getFileName();
                    }
                } else {
                    $class   = new \ReflectionClass($test);
                    $files[] = $class->getFileName();
                }
            }
        }

        return array_unique($files);

    }
}
