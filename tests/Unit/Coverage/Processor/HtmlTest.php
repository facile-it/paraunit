<?php

declare(strict_types=1);

namespace Tests\Unit\Coverage\Processor;

use Paraunit\Configuration\OutputPath;
use Paraunit\Coverage\Processor\Html;
use Tests\BaseUnitTestCase;

class HtmlTest extends BaseUnitTestCase
{
    public function testWriteToDir(): void
    {
        $targetPath = new OutputPath(sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'html');
        $text = new Html($targetPath);

        $this->assertDirectoryNotExists($targetPath->getPath());

        $text->process($this->createCodeCoverage());

        $this->assertDirectoryExists($targetPath->getPath());
        $index = $targetPath->getPath() . DIRECTORY_SEPARATOR . 'index.html';
        $content = $this->getFileContent($index);
        $this->removeDirectory($targetPath->getPath());

        $this->assertStringStartsWith('<!DOCTYPE html>', $content);
        $this->assertStringContainsString('<title>Code Coverage for', $content);
        $this->assertStringContainsString('</html>', $content);
    }
}
