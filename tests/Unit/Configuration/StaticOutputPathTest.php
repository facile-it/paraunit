<?php

namespace Tests\Unit\Configuration;

use Paraunit\Configuration\StaticOutputPath;
use Tests\BaseUnitTestCase;

/**
 * Class StaticOutputPathTest
 * @package Tests\Unit\Configuration
 */
class StaticOutputPathTest extends BaseUnitTestCase
{
    public function testGetPathThrowExceptionIfNotReady()
    {
        new StaticOutputPath(null);
        $this->setExpectedException('\RuntimeException');

        StaticOutputPath::getPath();
    }

    /***
     * @depends testGetPathThrowExceptionIfNotReady
     */
    public function testGetPath()
    {
        $instantiation = new StaticOutputPath($this->getStubPath());
        $this->assertInstanceOf('\PHPUnit_Framework_TestListener', $instantiation);

        $this->assertEquals($this->getStubPath(), StaticOutputPath::getPath());
    }
}
