<?php

namespace Tests\Unit\Coverage;

use Paraunit\Configuration\OutputFile;
use Paraunit\Configuration\OutputPath;
use Paraunit\Coverage\CoverageOutputPaths;
use Paraunit\Coverage\CoverageResult;
use Prophecy\Argument;

/**
 * Class CoverageResultTest
 * @package Tests\Unit\Coverage
 */
class CoverageResultTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider outputPathsProvider
     */
    public function testGenerateResults(CoverageOutputPaths $outputPaths)
    {
        $coverageResult = $this->createCoverageResultWithMocks($outputPaths);

        $coverageResult->generateResults();
    }

    /**
     * @param CoverageOutputPaths $outputPaths
     * @return CoverageResult
     */
    private function createCoverageResultWithMocks(CoverageOutputPaths $outputPaths)
    {
        $coverageData = $this->prophesize('\PHP_CodeCoverage');
        $merger = $this->prophesize('Paraunit\Coverage\CoverageMerger');
        $merger->getCoverageData()->shouldBeCalled()->willReturn($coverageData);

        $cloverResult = $this->prophesize('Paraunit\Proxy\Coverage\CloverResult');
        if ($outputPaths->getCloverFilePath()->isEmpty()) {
            $cloverResult->process(Argument::cetera())->shouldNotBeCalled();
        } else {
            $cloverResult->process($coverageData, $outputPaths->getCloverFilePath())->shouldBeCalled();
        }

        $xmlResult = $this->prophesize('Paraunit\Proxy\Coverage\XMLResult');
        if ($outputPaths->getXmlPath()->isEmpty()) {
            $xmlResult->process(Argument::cetera())->shouldNotBeCalled();
        } else {
            $xmlResult->process($coverageData, $outputPaths->getXmlPath())->shouldBeCalled();
        }

        $htmlResult = $this->prophesize('Paraunit\Proxy\Coverage\HTMLResult');
        if ($outputPaths->getHtmlPath()->isEmpty()) {
            $htmlResult->process(Argument::cetera())->shouldNotBeCalled();
        } else {
            $htmlResult->process($coverageData, $outputPaths->getHtmlPath())->shouldBeCalled();
        }


        $coverageResult = new CoverageResult(
            $merger->reveal(),
            $outputPaths,
            $cloverResult->reveal(),
            $xmlResult->reveal(),
            $htmlResult->reveal()
        );

        return $coverageResult;
    }

    public function outputPathsProvider()
    {
        return array(
            array(new CoverageOutputPaths(new OutputFile('file.xml'), $this->mockEmptyPath(), $this->mockEmptyPath())),
            array(new CoverageOutputPaths($this->mockEmptyFilePath(), new OutputPath('.'),    $this->mockEmptyPath())),
            array(new CoverageOutputPaths($this->mockEmptyFilePath(), $this->mockEmptyPath(), new OutputPath('.'))),
        );
    }

    /**
     * @return OutputFile
     */
    private function mockEmptyFilePath()
    {
        $emptyPath = $this->prophesize('Paraunit\Configuration\OutputFile');
        $emptyPath->isEmpty()->shouldBeCalled()->willReturn(true);

        return $emptyPath->reveal();
    }

    /**
     * @return OutputPath
     */
    private function mockEmptyPath()
    {
        $emptyPath = $this->prophesize('Paraunit\Configuration\OutputPath');
        $emptyPath->isEmpty()->shouldBeCalled()->willReturn(true);

        return $emptyPath->reveal();
    }
}
