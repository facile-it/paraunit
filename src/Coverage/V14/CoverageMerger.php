<?php

declare(strict_types=1);

namespace Paraunit\Coverage\V14;

use Paraunit\Lifecycle\ProcessParsingCompleted;
use Paraunit\Proxy\Coverage\FakeDriver;
use SebastianBergmann\CodeCoverage\CodeCoverage;
use SebastianBergmann\CodeCoverage\Filter;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class CoverageMerger implements EventSubscriberInterface
{
    private readonly CodeCoverage $coverageData;

    public function __construct(private readonly CoverageFetcher $coverageFetcher)
    {
        $this->coverageData = new CodeCoverage(new FakeDriver(), new Filter());
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

        $newCoverageData = $this->coverageFetcher->fetch($process);
        $this->coverageData->getData()->merge($newCoverageData);
    }

    public function getCoverageData(): CodeCoverage
    {
        return $this->coverageData;
    }
}
