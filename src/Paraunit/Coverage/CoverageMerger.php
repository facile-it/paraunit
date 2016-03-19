<?php

namespace Paraunit\Coverage;

use Paraunit\Lifecycle\ProcessEvent;
use Paraunit\Process\AbstractParaunitProcess;

/**
 * Class CoverageMerger
 * @package Paraunit\Coverage
 */
class CoverageMerger
{
    /** @var  CoverageFetcher */
    private $coverageFetcher;

    /** @var  \PHP_CodeCoverage */
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

        if ($this->coverageData instanceof \PHP_CodeCoverage) {
            $this->coverageData->merge($newCoverageData);
        } else {
            $this->coverageData = $newCoverageData;
        }
    }

    /**
     * @return \PHP_CodeCoverage
     */
    public function getCoverageData()
    {
        return $this->coverageData;
    }
}
