<?php

namespace Tests;

/**
 * Class BaseTestCase
 * @package Tests
 */
class BaseTestCase extends \PHPUnit_Framework_TestCase
{
    /**
     * @return string
     */
    protected function getCoverageStubFilePath()
    {
        $filename = __DIR__ . '/Stub/CoverageOutput/CoverageStub.php';
        $this->assertTrue(file_exists($filename), 'CoverageStub file missing!');

        return $filename;
    }
}
