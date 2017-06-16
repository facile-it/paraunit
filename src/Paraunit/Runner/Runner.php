<?php
declare(strict_types=1);

namespace Paraunit\Runner;

use Paraunit\Filter\Filter;
use Paraunit\Lifecycle\EngineEvent;
use Paraunit\Lifecycle\ProcessEvent;
use Paraunit\Process\ProcessBuilderFactory;
use Paraunit\Process\RetryAwareInterface;
use Paraunit\Process\SymfonyProcessWrapper;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * Class Runner
 * @package Paraunit\Runner
 */
class Runner
{
    /** @var  ProcessBuilderFactory */
    private $processBuilderFactory;

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
     * @param ProcessBuilderFactory $processFactory
     * @param Filter $filter
     * @param PipelineCollection $pipelineCollection
     */
    public function __construct(
        EventDispatcherInterface $eventDispatcher,
        ProcessBuilderFactory $processFactory,
        Filter $filter,
        PipelineCollection $pipelineCollection
    ) {
        $this->eventDispatcher = $eventDispatcher;
        $this->processBuilderFactory = $processFactory;
        $this->filter = $filter;
        $this->pipelineCollection = $pipelineCollection;
        $this->queuedProcesses = new \SplQueue();
        $this->exitCode = 0;
    }

    /**
     * @return int The final exit code: 0 if no failures, 10 otherwise
     */
    public function run(): int
    {
        $this->eventDispatcher->dispatch(EngineEvent::BEFORE_START);

        $this->createProcessQueue();

        $this->eventDispatcher->dispatch(EngineEvent::START);

        while ($this->pipelineCollection->checkRunningState() || $this->pushToPipeline()) {
            usleep(100);
        }

        $this->eventDispatcher->dispatch(EngineEvent::END);

        return $this->exitCode;
    }

    /**
     * @param ProcessEvent $processEvent
     */
    public function onProcessParsingCompleted(ProcessEvent $processEvent)
    {
        $process = $processEvent->getProcess();

        if ($process instanceof RetryAwareInterface && $process->isToBeRetried()) {
            $process->reset();
            $process->increaseRetryCount();

            $this->queuedProcesses->enqueue($process);
            
            $this->eventDispatcher->dispatch(ProcessEvent::PROCESS_TO_BE_RETRIED, new ProcessEvent($process));
        } elseif ($process->getExitCode() !== 0) {
            $this->exitCode = 10;
        }
    }

    private function createProcessQueue()
    {
        foreach ($this->filter->filterTestFiles() as $file) {
            $processBuilder = $this->processBuilderFactory->create($file);
            $this->queuedProcesses->enqueue(new SymfonyProcessWrapper($processBuilder, $file));
        }
    }

    private function pushToPipeline(): bool
    {
        $somethingHasBeenPushed = false;
        while (! $this->queuedProcesses->isEmpty() && $this->pipelineCollection->hasEmptySlots()) {
            $this->pipelineCollection->push($this->queuedProcesses->dequeue());
            $somethingHasBeenPushed = true;
        }
        
        return $somethingHasBeenPushed;
    }
}
