<?php

namespace Tests\Unit\Configuration;

use Paraunit\Configuration\OutputPath;

/**
 * Class OutputPathTest
 * @package Tests\Unit\Configuration
 */
class OutputPathTest extends \PHPUnit_Framework_TestCase
{
    public function testCostruct()
    {
        $outputPath = new OutputPath('sub/dir/from/relpath');

        $this->assertFalse($outputPath->isEmpty());
        $this->assertEquals('sub/dir/from/relpath', $outputPath->getPath());
    }

    /**
     * @dataProvider emptyPathsProvider
     */
    public function testIsEmpty($emptyPath)
    {
        $outputPath = new OutputPath($emptyPath);

        $this->assertTrue($outputPath->isEmpty());

        $this->setExpectedException('\RuntimeException');
        $outputPath->getPath();
    }

    public function emptyPathsProvider()
    {
        return array(
            array(null),
            array(''),
        );
    }

    public function testThatPathIsOk()
    {
        $this->markTestIncomplete('Not sure how to intercept invalid paths');
    }
}
