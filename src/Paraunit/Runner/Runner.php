<?php

declare(strict_types=1);

namespace Paraunit\Runner;

use Paraunit\Filter\Filter;
use Paraunit\Lifecycle\EngineEvent;
use Paraunit\Lifecycle\ProcessEvent;
use Paraunit\Process\ProcessFactoryInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Class Runner
 */
class Runner implements EventSubscriberInterface
{
    /** @var ProcessFactoryInterface */
    private $processFactory;

    /** @var EventDispatcherInterface */
    private $eventDispatcher;

    /** @var Filter */
    private $filter;

    /** @var PipelineCollection */
    private $pipelineCollection;

    /** @var \SplQueue */
    private $queuedProcesses;

    /** @var int */
    private $exitCode;

    /**
     * @param EventDispatcherInterface $eventDispatcher
     * @param ProcessFactoryInterface $processFactory
     * @param Filter $filter
     * @param PipelineCollection $pipelineCollection
     */
    public function __construct(
        EventDispatcherInterface $eventDispatcher,
        ProcessFactoryInterface $processFactory,
        Filter $filter,
        PipelineCollection $pipelineCollection
    ) {
        $this->eventDispatcher = $eventDispatcher;
        $this->processFactory = $processFactory;
        $this->filter = $filter;
        $this->pipelineCollection = $pipelineCollection;
        $this->queuedProcesses = new \SplQueue();
        $this->exitCode = 0;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            ProcessEvent::PROCESS_TERMINATED => 'pushToPipeline',
            ProcessEvent::PROCESS_TO_BE_RETRIED => 'onProcessToBeRetried',
            ProcessEvent::PROCESS_PARSING_COMPLETED => 'onProcessParsingCompleted',
        ];
    }

    /**
     * @return int The final exit code: 0 if no failures, 10 otherwise
     */
    public function run(): int
    {
        $this->eventDispatcher->dispatch(EngineEvent::BEFORE_START);

        $this->createProcessQueue();

        $this->eventDispatcher->dispatch(EngineEvent::START);

        do {
            $this->pushToPipeline();
            usleep(100);
            $this->pipelineCollection->triggerProcessTermination();
        } while (! $this->pipelineCollection->isEmpty() || ! $this->queuedProcesses->isEmpty());

        $this->eventDispatcher->dispatch(EngineEvent::END);

        return $this->exitCode;
    }

    /**
     * @param ProcessEvent $processEvent
     */
    public function onProcessParsingCompleted(ProcessEvent $processEvent)
    {
        if ($processEvent->getProcess()->getExitCode() !== 0) {
            $this->exitCode = 10;
        }
    }

    /**
     * @param ProcessEvent $processEvent
     */
    public function onProcessToBeRetried(ProcessEvent $processEvent)
    {
        $this->queuedProcesses->enqueue($processEvent->getProcess());
    }

    private function createProcessQueue()
    {
        foreach ($this->filter->filterTestFiles() as $file) {
            $this->queuedProcesses->enqueue(
                $this->processFactory->create($file)
            );
        }
    }

    public function pushToPipeline()
    {
        while (! $this->queuedProcesses->isEmpty() && $this->pipelineCollection->hasEmptySlots()) {
            $this->pipelineCollection->push($this->queuedProcesses->dequeue());
        }
    }
}
