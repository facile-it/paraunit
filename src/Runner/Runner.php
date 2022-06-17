<?php

declare(strict_types=1);

namespace Paraunit\Runner;

use function function_exists;
use Paraunit\Configuration\ChunkSize;
use Paraunit\Filter\Filter;
use Paraunit\Lifecycle\BeforeEngineStart;
use Paraunit\Lifecycle\EngineEnd;
use Paraunit\Lifecycle\EngineStart;
use Paraunit\Lifecycle\ProcessParsingCompleted;
use Paraunit\Lifecycle\ProcessTerminated;
use Paraunit\Lifecycle\ProcessToBeRetried;
use Paraunit\Process\AbstractParaunitProcess;
use Paraunit\Process\ProcessFactoryInterface;
use Psr\EventDispatcher\EventDispatcherInterface;
use RuntimeException;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

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

    /** @var ChunkSize */
    private $chunkSize;

    /** @var ChunkFile */
    private $chunkFile;

    /** @var \SplQueue<AbstractParaunitProcess> */
    private $queuedProcesses;

    /** @var int */
    private $exitCode;

    public function __construct(
        EventDispatcherInterface $eventDispatcher,
        ProcessFactoryInterface $processFactory,
        Filter $filter,
        PipelineCollection $pipelineCollection,
        ChunkSize $chunkSize,
        ChunkFile $chunkFile
    ) {
        $this->eventDispatcher = $eventDispatcher;
        $this->processFactory = $processFactory;
        $this->filter = $filter;
        $this->pipelineCollection = $pipelineCollection;
        $this->chunkSize = $chunkSize;
        $this->chunkFile = $chunkFile;
        /**
         * @psalm-suppress MixedPropertyTypeCoercion
         *
         * @see https://github.com/vimeo/psalm/issues/8103
         */
        $this->queuedProcesses = new \SplQueue();
        $this->exitCode = 0;

        if (function_exists('pcntl_async_signals') && function_exists('pcntl_signal')) {
            pcntl_async_signals(true);
            pcntl_signal(SIGINT, [$this, 'onShutdown']);
        }
    }

    /**
     * @return array<string, string>
     */
    public static function getSubscribedEvents(): array
    {
        return [
            ProcessTerminated::class => 'pushToPipeline',
            ProcessToBeRetried::class => 'onProcessToBeRetried',
            ProcessParsingCompleted::class => 'onProcessParsingCompleted',
        ];
    }

    /**
     * @return int The final exit code: 0 if no failures, 10 otherwise
     */
    public function run(): int
    {
        $this->eventDispatcher->dispatch(new BeforeEngineStart());

        if ($this->chunkSize->isChunked()) {
            $this->createChunkedProcessQueue();
        } else {
            $this->createProcessQueue();
        }

        $this->eventDispatcher->dispatch(new EngineStart());

        do {
            $this->pushToPipeline();
            usleep(100);
            $this->pipelineCollection->triggerProcessTermination();
        } while (! $this->pipelineCollection->isEmpty() || ! $this->queuedProcesses->isEmpty());

        $this->eventDispatcher->dispatch(new EngineEnd());

        return $this->exitCode;
    }

    public function onProcessParsingCompleted(ProcessParsingCompleted $processEvent): void
    {
        if ($processEvent->getProcess()->getExitCode() !== 0) {
            $this->exitCode = 10;
        }
    }

    public function onProcessToBeRetried(ProcessToBeRetried $processEvent): void
    {
        $this->queuedProcesses->enqueue($processEvent->getProcess());
    }

    private function createProcessQueue(): void
    {
        foreach ($this->filter->filterTestFiles() as $file) {
            $this->queuedProcesses->enqueue(
                $this->processFactory->create($file)
            );
        }
    }

    private function createChunkedProcessQueue(): void
    {
        $files = $this->filter->filterTestFiles();
        foreach (array_chunk($files, $this->chunkSize->getChunkSize()) as $chunkNumber => $filesChunk) {
            $chunkFileName = $this->chunkFile->createChunkFile($chunkNumber, $filesChunk);
            $this->queuedProcesses->enqueue(
                $this->processFactory->create($chunkFileName)
            );
        }
    }

    public function pushToPipeline(ProcessTerminated $event = null): void
    {
        if ($event && $this->chunkSize->isChunked()) {
            $process = $event->getProcess();
            if (! $process->isToBeRetried()) {
                $this->chunkFile->deleteChunkFile($process);
            }
        }

        while (! $this->queuedProcesses->isEmpty() && $this->pipelineCollection->hasEmptySlots()) {
            $this->pipelineCollection->push($this->queuedProcesses->dequeue());
        }
    }

    public function onShutdown(): void
    {
        $this->pipelineCollection->triggerProcessTermination();

        if ($this->chunkSize->isChunked()) {
            $processes = $this->pipelineCollection->getRunningProcesses();
            foreach ($processes as $process) {
                $this->chunkFile->deleteChunkFile($process);
            }
            do {
                try {
                    $this->chunkFile->deleteChunkFile($this->queuedProcesses->dequeue());
                } catch (RuntimeException $e) {
                    //pass
                }
            } while (! $this->queuedProcesses->isEmpty());
        }
    }
}
