<?php

namespace Paraunit\Coverage;

use Paraunit\Coverage\Processor\CoverageProcessorInterface;

/**
 * Class CoverageResult
 * @package Paraunit\Coverage
 */
class CoverageResult
{
    /** @var CoverageMerger */
    private $coverageMerger;

    /** @var CoverageProcessorInterface[] */
    private $coverageProcessors;

    /**
     * CoverageResult constructor.
     * @param CoverageMerger $coverageMerger
     */
    public function __construct(CoverageMerger $coverageMerger)
    {
        $this->coverageMerger = $coverageMerger;
        $this->coverageProcessors = [];
    }

    public function addCoverageProcessor(CoverageProcessorInterface $processor)
    {
        $this->coverageProcessors[] = $processor;
    }

    public function generateResults()
    {
        $coverageData = $this->coverageMerger->getCoverageData();

        foreach ($this->coverageProcessors as $processor) {
            $processor->process($coverageData);
        }
    }
}
