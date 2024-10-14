<?php

declare(strict_types=1);

namespace Paraunit\Runner;

use Paraunit\Configuration\ChunkSize;
use Paraunit\Filter\TestList;
use Paraunit\Lifecycle\BeforeEngineStart;
use Paraunit\Lifecycle\EngineEnd;
use Paraunit\Lifecycle\EngineStart;
use Paraunit\Lifecycle\ProcessParsingCompleted;
use Paraunit\Lifecycle\ProcessTerminated;
use Paraunit\Lifecycle\ProcessToBeRetried;
use Paraunit\Process\Process;
use Paraunit\Process\ProcessFactory;
use Psr\EventDispatcher\EventDispatcherInterface;
use RuntimeException;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

use function function_exists;

class Runner implements EventSubscriberInterface
{
    /** @var \SplQueue<Process> */
    private readonly \SplQueue $queuedProcesses;

    private int $exitCode = 0;

    public function __construct(
        private readonly EventDispatcherInterface $eventDispatcher,
        private readonly ProcessFactory $processFactory,
        private readonly TestList $testList,
        private readonly PipelineCollection $pipelineCollection,
        private readonly ChunkSize $chunkSize,
        private readonly ChunkFile $chunkFile
    ) {
        /**
         * @psalm-suppress MixedPropertyTypeCoercion
         *
         * @see https://github.com/vimeo/psalm/issues/8103
         */
        $this->queuedProcesses = new \SplQueue();

        if (function_exists('pcntl_async_signals') && function_exists('pcntl_signal')) {
            pcntl_async_signals(true);
            pcntl_signal(SIGINT, $this->onShutdown(...));
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
        foreach ($this->testList->getTests() as $file) {
            $this->queuedProcesses->enqueue(
                $this->processFactory->create($file)
            );
        }
    }

    private function createChunkedProcessQueue(): void
    {
        $files = $this->testList->getTests();
        foreach (array_chunk($files, $this->chunkSize->getChunkSize()) as $chunkNumber => $filesChunk) {
            $chunkFileName = $this->chunkFile->createChunkFile($chunkNumber, $filesChunk);
            $this->queuedProcesses->enqueue(
                $this->processFactory->create($chunkFileName)
            );
        }
    }

    public function pushToPipeline(?ProcessTerminated $event = null): void
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
                } catch (RuntimeException) {
                    // pass
                }
            } while (! $this->queuedProcesses->isEmpty());
        }
    }
}
