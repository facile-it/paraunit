<?php

namespace Paraunit\Coverage;

use Paraunit\Lifecycle\ProcessEvent;
use Paraunit\Process\AbstractParaunitProcess;
use SebastianBergmann\CodeCoverage\CodeCoverage;

/**
 * Class CoverageMerger
 * @package Paraunit\Coverage
 */
class CoverageMerger
{
    /** @var  CoverageFetcher */
    private $coverageFetcher;

    /** @var  CodeCoverage */
    private $coverageData;

    /**
     * CoverageMerger constructor.
     * @param CoverageFetcher $coverageFetcher
     */
    public function __construct(CoverageFetcher $coverageFetcher)
    {
        $this->coverageFetcher = $coverageFetcher;
    }

    /**
     * @param ProcessEvent $processEvent
     */
    public function onProcessTerminated(ProcessEvent $processEvent)
    {
        $this->merge($processEvent->getProcess());
    }

    /**
     * @param AbstractParaunitProcess $process
     */
    private function merge(AbstractParaunitProcess $process)
    {
        $newCoverageData = $this->coverageFetcher->fetch($process);

        if ($this->coverageData instanceof CodeCoverage) {
            $this->coverageData->merge($newCoverageData);
        } else {
            $this->coverageData = $newCoverageData;
        }
    }

    /**
     * @return CodeCoverage
     */
    public function getCoverageData()
    {
        return $this->coverageData;
    }
}
