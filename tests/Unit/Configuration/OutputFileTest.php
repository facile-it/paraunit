<?php

declare(strict_types=1);

namespace Tests\Unit\Configuration;

use Paraunit\Configuration\OutputFile;
use Tests\BaseUnitTestCase;

class OutputFileTest extends BaseUnitTestCase
{
    public function testConstruct(): void
    {
        $outputFile = new OutputFile('sub/dir/from/relfile.xml');

        $this->assertFalse($outputFile->isEmpty());
        $this->assertEquals('sub/dir/from/relfile.xml', $outputFile->getFilePath());
    }

    /**
     * @dataProvider emptyFilesProvider
     */
    public function testIsEmpty($emptyFile): void
    {
        $outputFile = new OutputFile($emptyFile);

        $this->assertTrue($outputFile->isEmpty());

        $this->expectException(\RuntimeException::class);
        $outputFile->getFilePath();
    }

    /**
     * @return mixed[][]
     */
    public function emptyFilesProvider(): array
    {
        return [
            [null],
            [''],
            [false],
        ];
    }
}
