<?php

declare(strict_types=1);

namespace Tests\Unit\Coverage\Processor;

use Paraunit\Configuration\OutputPath;
use Paraunit\Coverage\Processor\Html;
use SebastianBergmann\CodeCoverage\CodeCoverage;
use Tests\BaseUnitTestCase;

/**
 * Class HtmlTest
 * @package Tests\Unit\Proxy
 */
class HtmlTest extends BaseUnitTestCase
{
    public function testWriteToDir()
    {
        $targetPath = new OutputPath(sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'html');
        $text = new Html($targetPath);

        $this->assertDirectoryNotExists($targetPath->getPath());

        $text->process($this->createCodeCoverage());

        $this->assertDirectoryExists($targetPath->getPath());
        $index = $targetPath->getPath() . DIRECTORY_SEPARATOR . 'index.html';
        $this->assertFileExists($index);
        $content = file_get_contents($index);
        $this->removeDirectory($targetPath->getPath());

        $this->assertStringStartsWith('<!DOCTYPE html>', $content);
        $this->assertContains('<title>Code Coverage for', $content);
        $this->assertContains('</html>', $content);
    }
}
