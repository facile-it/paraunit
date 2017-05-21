<?php

namespace Tests;

use Paraunit\Configuration\PhpCodeCoverageCompat;
use PHPUnit\Framework\TestCase;

/**
 * Class BaseTestCase
 * @package Tests
 */
class BaseTestCase extends TestCase
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
        static::assertFileExists($filename, 'CoverageStub file missing!');

        return $filename;
    }

    /**
     * @return string
     */
    protected function getCoverage4StubFilePath()
    {
        $filename = __DIR__ . '/Stub/CoverageOutput/Coverage4Stub.php';
        static::assertFileExists($filename, 'CoverageStub file missing!');

        return $filename;
    }

    /**
     * @return string
     */
    protected function getConfigForStubs()
    {
        return $this->getStubPath() . 'phpunit_for_stubs.xml';
    }

    /**
     * @return string
     */
    protected function getStubPath()
    {
        return realpath(__DIR__ . DIRECTORY_SEPARATOR . 'Stub') . DIRECTORY_SEPARATOR;
    }
}
