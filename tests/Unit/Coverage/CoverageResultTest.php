<?php

namespace Tests\Unit\Coverage;

use Paraunit\Configuration\OutputFile;
use Paraunit\Configuration\OutputPath;
use Paraunit\Coverage\CoverageOutputPaths;
use Paraunit\Coverage\CoverageResult;
use Paraunit\Lifecycle\EngineEvent;
use Prophecy\Argument;
use Tests\BaseTestCase;

/**
 * Class CoverageResultTest
 * @package Tests\Unit\Coverage
 */
class CoverageResultTest extends BaseTestCase
{
    /**
     * @dataProvider outputPathsProvider
     */
    public function testGenerateResults(CoverageOutputPaths $outputPaths)
    {
        $output = $this->prophesize('Symfony\Component\Console\Output\OutputInterface');
        $output->write('colored coverage data')
            ->shouldBeCalledTimes((int)$outputPaths->isTextToConsoleEnabled());
        $engineEvent = new EngineEvent($output->reveal());

        $coverageResult = $this->createCoverageResultWithMocks($outputPaths);

        $coverageResult->generateResults($engineEvent);
    }

    /**
     * @param CoverageOutputPaths $outputPaths
     * @return CoverageResult
     */
    private function createCoverageResultWithMocks(CoverageOutputPaths $outputPaths)
    {
        $coverageData = $this->prophesize('Paraunit\Proxy\Coverage\CodeCoverage');
        $merger = $this->prophesize('Paraunit\Coverage\CoverageMerger');
        $merger->getCoverageData()->shouldBeCalled()->willReturn($coverageData);

        $cloverResult = $this->prophesize('Paraunit\Proxy\Coverage\CloverResult');
        if ($outputPaths->getCloverFilePath()->isEmpty()) {
            $cloverResult->process(Argument::cetera())->shouldNotBeCalled();
        } else {
            $cloverResult->process($coverageData, $outputPaths->getCloverFilePath())->shouldBeCalled();
        }

        $xmlResult = $this->prophesize('Paraunit\Proxy\Coverage\XmlResult');
        if ($outputPaths->getXmlPath()->isEmpty()) {
            $xmlResult->process(Argument::cetera())->shouldNotBeCalled();
        } else {
            $xmlResult->process($coverageData, $outputPaths->getXmlPath())->shouldBeCalled();
        }

        $htmlResult = $this->prophesize('Paraunit\Proxy\Coverage\HtmlResult');
        if ($outputPaths->getHtmlPath()->isEmpty()) {
            $htmlResult->process(Argument::cetera())->shouldNotBeCalled();
        } else {
            $htmlResult->process($coverageData, $outputPaths->getHtmlPath())->shouldBeCalled();
        }

        $textResult = $this->prophesize('Paraunit\Proxy\Coverage\TextResult');
        if (! $outputPaths->isTextToConsoleEnabled() && $outputPaths->getTextFile()->isEmpty()) {
            $textResult->process(Argument::cetera())->shouldNotBeCalled();
        }

        if ($outputPaths->isTextToConsoleEnabled()) {
            $textResult->process($coverageData, false)
                ->shouldBeCalled()
                ->willReturn('colored coverage data');
        }

        if (! $outputPaths->getTextFile()->isEmpty()) {
            $textResult->writeToFile($coverageData, Argument::cetera())->shouldBeCalled();
        }

        $coverageResult = new CoverageResult(
            $merger->reveal(),
            $outputPaths,
            $cloverResult->reveal(),
            $xmlResult->reveal(),
            $htmlResult->reveal(),
            $textResult->reveal()
        );

        return $coverageResult;
    }

    public function outputPathsProvider()
    {
        return array(
            array(
                new CoverageOutputPaths(
                    new OutputFile('file.xml'),
                    $this->mockEmptyPath(),
                    $this->mockEmptyPath(),
                    $this->mockEmptyFilePath(),
                    false
                )
            ),
            array(
                new CoverageOutputPaths(
                    $this->mockEmptyFilePath(),
                    new OutputPath('.'),
                    $this->mockEmptyPath(),
                    $this->mockEmptyFilePath(),
                    false
                )
            ),
            array(
                new CoverageOutputPaths(
                    $this->mockEmptyFilePath(),
                    $this->mockEmptyPath(),
                    new OutputPath('.'),
                    $this->mockEmptyFilePath(),
                    false
                )
            ),
            array(
                new CoverageOutputPaths(
                    $this->mockEmptyFilePath(),
                    $this->mockEmptyPath(),
                    $this->mockEmptyPath(),
                    new OutputFile('cov.txt'),
                    false
                )
            ),
            array(
                new CoverageOutputPaths(
                    $this->mockEmptyFilePath(),
                    $this->mockEmptyPath(),
                    $this->mockEmptyPath(),
                    $this->mockEmptyFilePath(),
                    true
                )
            ),
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
