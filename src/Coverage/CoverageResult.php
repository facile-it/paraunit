<?php

declare(strict_types=1);

namespace Paraunit\Coverage;

use Paraunit\Coverage\Processor\CoverageProcessorInterface;
use Paraunit\Lifecycle\EngineEnd;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class CoverageResult implements EventSubscriberInterface
{
    /** @var CoverageProcessorInterface[] */
    private array $coverageProcessors = [];

    public function __construct(private readonly CoverageMerger $coverageMerger) {}

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
