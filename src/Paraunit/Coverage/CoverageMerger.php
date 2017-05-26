<?php
declare(strict_types=1);

namespace Paraunit\Coverage;

use Paraunit\Lifecycle\ProcessEvent;
use Paraunit\Process\AbstractParaunitProcess;
use Paraunit\Process\ParaunitProcessInterface;
use Paraunit\Proxy\Coverage\CodeCoverage;

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
     * @param ParaunitProcessInterface $process
     */
    private function merge(ParaunitProcessInterface $process)
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
    public function getCoverageData(): CodeCoverage
    {
        return $this->coverageData;
    }
}
