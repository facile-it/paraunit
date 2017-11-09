<?php

declare(strict_types=1);

namespace Tests\Unit\Coverage\Processor;

use Paraunit\Configuration\OutputFile;
use Paraunit\Coverage\Processor\Text;
use Paraunit\Proxy\Coverage\CodeCoverage;
use Tests\BaseUnitTestCase;

/**
 * Class TextTest
 * @package Tests\Unit\Proxy
 */
class TextTest extends BaseUnitTestCase
{
    public function testWriteToFile()
    {
        $targetFile = new OutputFile(sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'coverage.txt');
        $text = new Text($targetFile);

        $this->assertFileNotExists($targetFile->getFilePath());

        $text->process(new CodeCoverage());

        $this->assertFileExists($targetFile->getFilePath());
        $content = file_get_contents($targetFile->getFilePath());
        unlink($targetFile->getFilePath());
        $this->assertContains('Code Coverage Report:', $content);
    }
}
