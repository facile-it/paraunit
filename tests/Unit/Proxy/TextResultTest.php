<?php


namespace Tests\Unit\Proxy;

use Paraunit\Configuration\OutputFile;
use Paraunit\Proxy\Coverage\CodeCoverage;
use Paraunit\Proxy\Coverage\TextResult;
use Tests\BaseUnitTestCase;

/**
 * Class TextResultTest
 * @package Tests\Unit\Proxy
 */
class TextResultTest extends BaseUnitTestCase
{
    public function testWriteToFile()
    {
        $textResult = new TextResult();
        $outputFile = new OutputFile(sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'coverage.txt');

        $this->assertFileNotExists($outputFile->getFilePath());
        
        $textResult->writeToFile(new CodeCoverage(), $outputFile);

        $this->assertFileExists($outputFile->getFilePath());
        $content = file_get_contents($outputFile->getFilePath());
        unlink($outputFile->getFilePath());
        $this->assertNotEmpty($content);
    }
}
