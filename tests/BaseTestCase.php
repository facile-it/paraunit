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
}
