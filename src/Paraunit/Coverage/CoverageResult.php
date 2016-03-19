<?php

namespace Paraunit\Coverage;

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

    /**
     * CoverageResult constructor.
     * @param CoverageMerger $coverageMerger
     * @param CoverageOutputPaths $coverageOutputPaths
     */
    public function __construct(CoverageMerger $coverageMerger, CoverageOutputPaths $coverageOutputPaths)
    {
        $this->coverageMerger = $coverageMerger;
        $this->coverageOutputPaths = $coverageOutputPaths;
    }

    public function generateResults()
    {
       // todo
    }
}
