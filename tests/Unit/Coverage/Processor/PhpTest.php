<?php

declare(strict_types=1);

namespace Tests\Unit\Coverage\Processor;

use Paraunit\Configuration\OutputFile;
use Paraunit\Coverage\Processor\Php;
use SebastianBergmann\CodeCoverage\Serialization\Serializer;
use Tests\BaseUnitTestCase;

class PhpTest extends BaseUnitTestCase
{
    public function testWriteToFile(): void
    {
        $filePath = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'coverage.php';
        $targetFile = new OutputFile($filePath);
        $text = new Php(new Serializer(), $targetFile);

        $this->assertFileDoesNotExist($targetFile->getFilePath());

        $text->process($this->createCodeCoverage());

        $content = $this->getFileContent($targetFile->getFilePath());
        unlink($targetFile->getFilePath());
        $this->assertStringStartsWith('<?php', $content);
        $this->assertStringContainsString('return \\unserialize', $content);
    }
}
