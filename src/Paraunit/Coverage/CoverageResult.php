<?php

namespace Paraunit\Coverage;

use Paraunit\Proxy\Coverage\CloverResult;
use Paraunit\Proxy\Coverage\HTMLResult;
use Paraunit\Proxy\Coverage\XMLResult;

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

    /** @var  XMLResult */
    private $xmlResult;

    /** @var  HTMLResult */
    private $htmlResult;

    /**
     * CoverageResult constructor.
     * @param CoverageMerger $coverageMerger
     * @param CoverageOutputPaths $coverageOutputPaths
     * @param CloverResult $cloverResult
     * @param XMLResult $xmlResult
     * @param HTMLResult $htmlResult
     */
    public function __construct(
        CoverageMerger $coverageMerger,
        CoverageOutputPaths $coverageOutputPaths,
        CloverResult $cloverResult,
        XMLResult $xmlResult,
        HTMLResult $htmlResult
    )
    {
        $this->coverageMerger = $coverageMerger;
        $this->coverageOutputPaths = $coverageOutputPaths;
        $this->cloverResult = $cloverResult;
        $this->xmlResult = $xmlResult;
        $this->htmlResult = $htmlResult;
    }

    public function generateResults()
    {
        $coverageData = $this->coverageMerger->getCoverageData();

        $cloverFilePath = $this->coverageOutputPaths->getCloverFilePath();
        if ( ! $cloverFilePath->isEmpty()) {
            $this->cloverResult->process($coverageData, $cloverFilePath);
        }

        $xmlPath = $this->coverageOutputPaths->getXmlPath();
        if ( ! $xmlPath->isEmpty()) {
            $this->xmlResult->process($coverageData, $xmlPath);
        }

        $htmlPath = $this->coverageOutputPaths->getHtmlPath();
        if ( ! $htmlPath->isEmpty()) {
            $this->htmlResult->process($coverageData, $htmlPath);
        }
    }
}
