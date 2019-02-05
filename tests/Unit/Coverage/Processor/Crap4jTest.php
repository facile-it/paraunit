<?php

declare(strict_types=1);

namespace Tests\Unit\Coverage\Processor;

use Paraunit\Configuration\OutputFile;
use Paraunit\Coverage\Processor\Crap4j;
use Tests\BaseUnitTestCase;

/**
 * Class Crap4jTest
 * @package Tests\Unit\Coverage\Processor
 */
class Crap4jTest extends BaseUnitTestCase
{
    public function testWriteToFile()
    {
        $targetFile = new OutputFile(sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'crap4j.xml');
        $text = new Crap4j($targetFile);

        $this->assertFileNotExists($targetFile->getFilePath());

        $text->process($this->createCodeCoverage());

        $content = $this->getFileContent($targetFile->getFilePath());
        unlink($targetFile->getFilePath());
        $this->assertStringStartsWith('<?xml', $content);
        $this->assertContains('<crap_result>', $content);
        $this->assertContains('</crap_result>', $content);
    }
}
