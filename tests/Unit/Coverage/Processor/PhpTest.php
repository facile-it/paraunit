<?php

declare(strict_types=1);

namespace Tests\Unit\Coverage\Processor;

use Paraunit\Configuration\OutputFile;
use Paraunit\Coverage\Processor\Php;
use Tests\BaseUnitTestCase;

class PhpTest extends BaseUnitTestCase
{
    public function testWriteToFile(): void
    {
        $targetFile = new OutputFile(sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'coverage.php');
        $text = new Php($targetFile);

        $this->assertFileDoesNotExist($targetFile->getFilePath());

        $text->process($this->createCodeCoverage());

        $content = $this->getFileContent($targetFile->getFilePath());
        unlink($targetFile->getFilePath());
        $this->assertStringStartsWith('<?php', $content);
        $this->assertStringContainsString('$coverage = new', $content);
        $this->assertStringContainsString('return $coverage;', $content);
    }
}
