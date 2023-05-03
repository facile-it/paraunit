<?php

declare(strict_types=1);

namespace Paraunit\Coverage;

use Paraunit\Lifecycle\ProcessParsingCompleted;
use Paraunit\Process\Process;
use SebastianBergmann\CodeCoverage\CodeCoverage;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class CoverageMerger implements EventSubscriberInterface
{
    private ?CodeCoverage $coverageData = null;

    public function __construct(private readonly CoverageFetcher $coverageFetcher)
    {
    }

    /**
     * @return array<string, string>
     */
    public static function getSubscribedEvents(): array
    {
        return [
            ProcessParsingCompleted::class => 'onProcessParsingCompleted',
        ];
    }

    public function onProcessParsingCompleted(ProcessParsingCompleted $processEvent): void
    {
        $process = $processEvent->getProcess();
        if ($process->isToBeRetried()) {
            return;
        }

        $this->merge($process);
    }

    private function merge(Process $process): void
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
        if ($this->coverageData instanceof CodeCoverage) {
            return $this->coverageData;
        }

        throw new \RuntimeException('Coverage data not ready');
    }
}
