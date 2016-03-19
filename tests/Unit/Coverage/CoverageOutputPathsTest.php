<?php

namespace Tests\Unit\Coverage;
use Paraunit\Configuration\OutputFile;
use Paraunit\Configuration\OutputPath;
use Paraunit\Coverage\CoverageOutputPaths;

/**
 * Class CoverageOutputPathsTest
 * @package Tests\Unit\Coverage
 */
class CoverageOutputPathsTest extends \PHPUnit_Framework_TestCase
{
    public function testGetters()
    {
        $clover = new OutputFile('clover');
        $xml = new OutputPath('xml');
        $html = new OutputPath('html');

        $outputPaths = new CoverageOutputPaths($clover, $xml, $html);

        $this->assertSame($clover, $outputPaths->getCloverFilePath());
        $this->assertSame($xml, $outputPaths->getXmlPath());
        $this->assertSame($html, $outputPaths->getHtmlPath());
    }
}
