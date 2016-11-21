<?php

namespace Tests\Unit\Configuration;

use Paraunit\Configuration\OutputFile;

/**
 * Class OutputFileTest
 * @package Tests\Unit\Configuration
 */
class OutputFileTest extends \PHPUnit_Framework_TestCase
{
    public function testCostruct()
    {
        $outputFile = new OutputFile('sub/dir/from/relfile.xml');

        $this->assertFalse($outputFile->isEmpty());
        $this->assertEquals('sub/dir/from/relfile.xml', $outputFile->getFilePath());
    }

    /**
     * @dataProvider emptyFilesProvider
     */
    public function testIsEmpty($emptyFile)
    {
        $outputFile = new OutputFile($emptyFile);

        $this->assertTrue($outputFile->isEmpty());

        $this->setExpectedException('\RuntimeException');
        $outputFile->getFilePath();
    }

    public function emptyFilesProvider()
    {
        return array(
            array(null),
            array(''),
        );
    }

    public function testThatFileIsOk()
    {
        $this->markTestIncomplete('Not sure how to intercept invalid files');
    }
}
