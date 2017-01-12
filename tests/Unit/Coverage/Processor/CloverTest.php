<?php

namespace Tests\Unit\Coverage\Processor;

use Paraunit\Configuration\OutputFile;
use Paraunit\Coverage\Processor\Clover;
use Paraunit\Coverage\Processor\Text;
use Paraunit\Proxy\Coverage\CodeCoverage;
use Tests\BaseUnitTestCase;

/**
 * Class CloverTest
 * @package Tests\Unit\Proxy
 */
class CloverTest extends BaseUnitTestCase
{
    public function testWriteToFile()
    {
        $targetFile = new OutputFile(sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'clover.xml');
        $text = new Clover($targetFile);

        $this->assertFileNotExists($targetFile->getFilePath());

        $text->process(new CodeCoverage());

        $this->assertFileExists($targetFile->getFilePath());
        $content = file_get_contents($targetFile->getFilePath());
        unlink($targetFile->getFilePath());
        $this->assertStringStartsWith('<?xml', $content);
        $this->assertContains('<coverage generated=', $content);
        $this->assertContains('</coverage>', $content);
    }
}
