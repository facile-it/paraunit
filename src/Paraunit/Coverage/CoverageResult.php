<?php

declare(strict_types=1);

namespace Paraunit\Coverage;

use Paraunit\Coverage\Processor\CoverageProcessorInterface;
use Paraunit\Lifecycle\EngineEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class CoverageResult implements EventSubscriberInterface
{
    /** @var CoverageMerger */
    private $coverageMerger;

    /** @var CoverageProcessorInterface[] */
    private $coverageProcessors;

    public function __construct(CoverageMerger $coverageMerger)
    {
        $this->coverageMerger = $coverageMerger;
        $this->coverageProcessors = [];
    }

    public static function getSubscribedEvents(): array
    {
        return [
            EngineEvent::END => 'generateResults',
        ];
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
