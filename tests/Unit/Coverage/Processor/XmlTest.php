<?php

declare(strict_types=1);

namespace Tests\Unit\Coverage\Processor;

use Paraunit\Configuration\OutputPath;
use Paraunit\Coverage\Processor\Xml;
use Tests\BaseUnitTestCase;

class XmlTest extends BaseUnitTestCase
{
    public function testWriteToFile(): void
    {
        $targetPath = new OutputPath(sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'xml');
        $text = new Xml($targetPath);

        $this->assertDirectoryNotExists($targetPath->getPath());

        $text->process($this->createCodeCoverage());

        $this->assertDirectoryExists($targetPath->getPath());
        $index = $targetPath->getPath() . DIRECTORY_SEPARATOR . 'index.xml';
        $content = $this->getFileContent($index);
        $this->removeDirectory($targetPath->getPath());

        $this->assertStringStartsWith('<?xml version="1.0"?>', $content);
        $this->assertContains('<phpunit xmlns="http', $content);
        $this->assertContains('</phpunit>', $content);
    }
}
