<?php

namespace Tests\Unit\Configuration;

use Paraunit\Configuration\OutputPath;
use Tests\BaseUnitTestCase;

/**
 * Class OutputPathTest
 * @package Tests\Unit\Configuration
 */
class OutputPathTest extends BaseUnitTestCase
{
    public function testConstruct()
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

        $this->expectException(\RuntimeException::class);
        $outputPath->getPath();
    }

    public function emptyPathsProvider(): array
    {
        return [
            [null],
            [''],
        ];
    }

    public function testThatPathIsOk()
    {
        $this->markTestIncomplete('Not sure how to intercept invalid paths');
    }
}
