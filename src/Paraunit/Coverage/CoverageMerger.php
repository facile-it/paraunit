<?php

declare(strict_types=1);

namespace Paraunit\Coverage;

use Paraunit\Lifecycle\ProcessEvent;
use Paraunit\Process\AbstractParaunitProcess;
use Paraunit\Proxy\Coverage\CodeCoverage;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Class CoverageMerger
 * @package Paraunit\Coverage
 */
class CoverageMerger implements EventSubscriberInterface
{
    /** @var CoverageFetcher */
    private $coverageFetcher;

    /** @var CodeCoverage */
    private $coverageData;

    /**
     * CoverageMerger constructor.
     * @param CoverageFetcher $coverageFetcher
     */
    public function __construct(CoverageFetcher $coverageFetcher)
    {
        $this->coverageFetcher = $coverageFetcher;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            ProcessEvent::PROCESS_PARSING_COMPLETED => 'onProcessParsingCompleted',
        ];
    }

    /**
     * @param ProcessEvent $processEvent
     */
    public function onProcessParsingCompleted(ProcessEvent $processEvent)
    {
        $process = $processEvent->getProcess();
        if ($process->isToBeRetried()) {
            return;
        }

        $this->merge($process);
    }

    private function merge(AbstractParaunitProcess $process)
    {
        $newCoverageData = $this->coverageFetcher->fetch($process);

        if ($this->coverageData instanceof CodeCoverage) {
            $this->coverageData->merge($newCoverageData);
        } else {
            $this->coverageData = $newCoverageData;
        }
    }

    public function getCoverageData(): CodeCoverage
    {
        return $this->coverageData;
    }
}
