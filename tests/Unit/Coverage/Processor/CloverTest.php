<?php

declare(strict_types=1);

namespace Tests\Unit\Coverage\Processor;

use Paraunit\Configuration\OutputFile;
use Paraunit\Coverage\Processor\Clover;
use Tests\BaseUnitTestCase;

class CloverTest extends BaseUnitTestCase
{
    public function testWriteToFile(): void
    {
        $targetFile = new OutputFile(sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'clover.xml');
        $text = new Clover($targetFile);

        $this->assertFileNotExists($targetFile->getFilePath());

        $text->process($this->createCodeCoverage());

        $content = $this->getFileContent($targetFile->getFilePath());
        unlink($targetFile->getFilePath());
        $this->assertStringStartsWith('<?xml', $content);
        $this->assertStringContainsString('<coverage generated=', $content);
        $this->assertStringContainsString('</coverage>', $content);
    }
}
