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

        $this->assertEquals('sub/dir/from/relfile.xml', $outputFile->getFilePath());
    }

    public function testIsEmpty(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Empty path provided: not valid');

        new OutputFile('');
    }
}
