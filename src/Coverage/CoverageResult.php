<?php

declare(strict_types=1);

namespace Paraunit\Coverage;

use Paraunit\Coverage\Processor\CoverageProcessorInterface;
use Paraunit\Lifecycle\EngineEnd;
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

    /**
     * @return array<string, string>
     */
    public static function getSubscribedEvents(): array
    {
        return [
            EngineEnd::class => 'generateResults',
        ];
    }

    public function addCoverageProcessor(CoverageProcessorInterface $processor): void
    {
        $this->coverageProcessors[] = $processor;
    }

    public function generateResults(): void
    {
        $coverageData = $this->coverageMerger->getCoverageData();

        foreach ($this->coverageProcessors as $processor) {
            $processor->process($coverageData);
        }
    }
}
