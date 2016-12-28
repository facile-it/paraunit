<?php

namespace Paraunit\Coverage;

use Paraunit\Lifecycle\EngineEvent;
use Paraunit\Proxy\Coverage\CloverResult;
use Paraunit\Proxy\Coverage\HtmlResult;
use Paraunit\Proxy\Coverage\TextResult;
use Paraunit\Proxy\Coverage\XmlResult;

/**
 * Class CoverageResult
 * @package Paraunit\Coverage
 */
class CoverageResult
{
    /** @var  CoverageMerger */
    private $coverageMerger;

    /** @var  CoverageOutputPaths */
    private $coverageOutputPaths;

    /** @var  CloverResult */
    private $cloverResult;

    /** @var  XmlResult */
    private $xmlResult;

    /** @var  HtmlResult */
    private $htmlResult;

    /** @var  TextResult */
    private $textResult;

    /**
     * CoverageResult constructor.
     * @param CoverageMerger $coverageMerger
     * @param CoverageOutputPaths $coverageOutputPaths
     * @param CloverResult $cloverResult
     * @param XmlResult $xmlResult
     * @param HtmlResult $htmlResult
     * @param TextResult $textResult
     */
    public function __construct(
        CoverageMerger $coverageMerger,
        CoverageOutputPaths $coverageOutputPaths,
        CloverResult $cloverResult,
        XmlResult $xmlResult,
        HtmlResult $htmlResult,
        TextResult $textResult
    ) {
        $this->coverageMerger = $coverageMerger;
        $this->coverageOutputPaths = $coverageOutputPaths;
        $this->cloverResult = $cloverResult;
        $this->xmlResult = $xmlResult;
        $this->htmlResult = $htmlResult;
        $this->textResult = $textResult;
    }

    public function generateResults(EngineEvent $event)
    {
        $coverageData = $this->coverageMerger->getCoverageData();

        $cloverFilePath = $this->coverageOutputPaths->getCloverFilePath();
        if (! $cloverFilePath->isEmpty()) {
            $this->cloverResult->process($coverageData, $cloverFilePath);
        }

        $xmlPath = $this->coverageOutputPaths->getXmlPath();
        if (! $xmlPath->isEmpty()) {
            $this->xmlResult->process($coverageData, $xmlPath);
        }

        $htmlPath = $this->coverageOutputPaths->getHtmlPath();
        if (! $htmlPath->isEmpty()) {
            $this->htmlResult->process($coverageData, $htmlPath);
        }

        $textFile = $this->coverageOutputPaths->getTextFile();
        if (! $textFile->isEmpty()) {
            $this->textResult->writeToFile($coverageData, $textFile);
        }

        if ($this->coverageOutputPaths->isTextToConsoleEnabled()) {
            $output = $event->getOutputInterface();
            $output->write($this->textResult->process($coverageData, true));
        }
    }
}
