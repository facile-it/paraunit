<?php

namespace Paraunit\Tests;


use Paraunit\Configuration\Paraunit;

/**
 * Class BaseTestCase
 * @package Paraunit\Tests
 */
abstract class BaseTestCase extends \PHPUnit_Framework_TestCase
{
    protected function setUp()
    {
        parent::setUp();

        $this->cleanUpTempDir(Paraunit::getTempBaseDir());
    }

    protected function tearDown()
    {
        parent::tearDown();

        $this->cleanUpTempDir(Paraunit::getTempBaseDir());
    }

    /**
     * @param string $dir
     */
    private function cleanUpTempDir($dir)
    {
        $it = new \RecursiveDirectoryIterator($dir, \RecursiveDirectoryIterator::SKIP_DOTS);
        $files = new \RecursiveIteratorIterator($it, \RecursiveIteratorIterator::CHILD_FIRST);

        foreach($files as $file) {
            if ($file->isDir()){
                rmdir($file->getRealPath());
            } else {
                unlink($file->getRealPath());
            }
        }

        rmdir($dir);
    }
}
