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
     * Fallback method for PHPUnit < 5.6
     *
     * @param string $filename
     * @param string $message
     */
    public static function assertFileExists($filename, $message = 'The specified file does not exists')
    {
        if (method_exists('\PHPUnit_Framework_TestCase', __METHOD__)) {
            static::assertFileExists($filename, $message);
        }

        static::assertTrue(file_exists($filename), $message);
    }

    /**
     * Fallback method for PHPUnit < 5.6
     *
     * @param string $filename
     * @param string $message
     */
    public static function assertFileNotExists($filename, $message = 'The specified file exists')
    {
        if (method_exists('\PHPUnit_Framework_TestCase', __METHOD__)) {
            static::assertFileNotExists($filename, $message);
        }

        static::assertFalse(file_exists($filename), $message);
    }

    /**
     * Fallback method for PHPUnit < 5.6
     *
     * @param string $dirname
     * @param string $message
     */
    public static function assertDirExists($dirname, $message = 'The specified dir does not exists')
    {
        if (method_exists('\PHPUnit_Framework_TestCase', __METHOD__)) {
            static::assertDirExists($dirname, $message);
        }

        static::assertTrue(is_dir($dirname), $message);
    }

    /**
     * Fallback method for PHPUnit < 5.6
     *
     * @param string $dirname
     * @param string $message
     */
    public static function assertDirNotExists($dirname, $message = 'The specified dir exists')
    {
        if (method_exists('\PHPUnit_Framework_TestCase', __METHOD__)) {
            static::assertDirNotExists($dirname, $message);
        }

        static::assertFalse(is_dir($dirname), $message);
    }
}
