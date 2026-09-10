<?php

declare(strict_types=1);

namespace Tests\Unit\Coverage\Processor;

use Paraunit\Configuration\OutputFile;
use Paraunit\Coverage\Processor\Cobertura;
use Tests\BaseUnitTestCase;

class CoberturaTest extends BaseUnitTestCase
{
    public function testWriteToFile(): void
    {
        $targetFile = new OutputFile(sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'cobertura.xml');
        $text = new Cobertura($targetFile);

        $this->assertFileDoesNotExist($targetFile->getFilePath());

        $text->process($this->createCodeCoverage());

        $content = $this->getFileContent($targetFile->getFilePath());
        unlink($targetFile->getFilePath());
        $this->assertStringStartsWith('<?xml', $content);
        $this->assertStringContainsString('<coverage line-rate="', $content);
        $this->assertStringContainsString('</coverage>', $content);
    }
}
