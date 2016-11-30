<?php

namespace Tests;

use Paraunit\Configuration\PhpCodeCoverageCompat;

/**
 * Class BaseTestCase
 * @package Tests
 */
class BaseTestCase extends \PHPUnit_Framework_TestCase
{
    public static function setUpBeforeClass()
    {
        parent::setUpBeforeClass();
        PhpCodeCoverageCompat::load();
    }

    /**
     * @return string
     */
    protected function getCoverageStubFilePath()
    {
        $filename = __DIR__ . '/Stub/CoverageOutput/CoverageStub.php';
        $this->assertFileExists($filename, 'CoverageStub file missing!');

        return $filename;
    }

    /**
     * @return string
     */
    protected function getCoverage4StubFilePath()
    {
        $filename = __DIR__ . '/Stub/CoverageOutput/Coverage4Stub.php';
        $this->assertFileExists($filename, 'CoverageStub file missing!');

        return $filename;
    }
}
