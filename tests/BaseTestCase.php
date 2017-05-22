<?php

namespace Tests;

use PHPUnit\Framework\TestCase;

/**
 * Class BaseTestCase
 * @package Tests
 */
class BaseTestCase extends TestCase
{
    protected function getCoverageStubFilePath(): string
    {
        $filename = __DIR__ . '/Stub/CoverageOutput/CoverageStub.php';
        static::assertFileExists($filename, 'CoverageStub file missing!');

        return $filename;
    }

    protected function getCoverage4StubFilePath(): string
    {
        $filename = __DIR__ . '/Stub/CoverageOutput/Coverage4Stub.php';
        static::assertFileExists($filename, 'CoverageStub file missing!');

        return $filename;
    }

    protected function getConfigForStubs(): string
    {
        return $this->getStubPath() . 'phpunit_for_stubs.xml';
    }

    protected function getStubPath(): string
    {
        return realpath(__DIR__ . DIRECTORY_SEPARATOR . 'Stub') . DIRECTORY_SEPARATOR;
    }
}
