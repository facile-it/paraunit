<?php
declare(strict_types=1);

namespace Tests\Unit\Configuration;

use Paraunit\Configuration\StaticOutputPath;
use PHPUnit\Framework\TestListener;
use Tests\BaseUnitTestCase;

/**
 * Class StaticOutputPathTest
 * @package Tests\Unit\Configuration
 */
class StaticOutputPathTest extends BaseUnitTestCase
{
    public function testGetPathThrowExceptionIfNotReady()
    {
        $this->expectException(\RuntimeException::class);

        StaticOutputPath::getPath();
    }

    /***
     * @depends testGetPathThrowExceptionIfNotReady
     */
    public function testGetPath()
    {
        $instantiation = new StaticOutputPath($this->getStubPath());
        $this->assertInstanceOf(TestListener::class, $instantiation);

        $this->assertEquals($this->getStubPath(), StaticOutputPath::getPath());
    }
}
