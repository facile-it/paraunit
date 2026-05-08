<?php

declare(strict_types=1);

namespace Tests\Unit\Runner;

use Paraunit\Configuration\ChunkSize;
use Paraunit\Filter\TestList;
use Paraunit\Lifecycle\BeforeEngineStart;
use Paraunit\Lifecycle\EngineEnd;
use Paraunit\Lifecycle\EngineStart;
use Paraunit\Lifecycle\ProcessParsingCompleted;
use Paraunit\Lifecycle\ProcessToBeRetried;
use Paraunit\Lifecycle\TestCompleted;
use Paraunit\Logs\ValueObject\Test;
use Paraunit\Process\Process;
use Paraunit\Process\ProcessFactory;
use Paraunit\Runner\ChunkFile;
use Paraunit\Runner\Pipeline;
use Paraunit\Runner\PipelineCollection;
use Paraunit\Runner\Runner;
use Paraunit\Runner\RunnerConfiguration;
use Paraunit\TestResult\ValueObject\TestIssue;
use Paraunit\TestResult\ValueObject\TestOutcome;
use PHPUnit\Framework\Attributes\DataProvider;
use Prophecy\Argument;
use Psr\EventDispatcher\EventDispatcherInterface;
use Tests\BaseUnitTestCase;
use Tests\Stub\StubbedParaunitProcess;

class RunnerTest extends BaseUnitTestCase
{
    /**
     * @return \Generator<string, array{TestIssue|TestOutcome}>
     */
    public static function completedOutcomeProvider(): \Generator
    {
        foreach (TestIssue::cases() as $case) {
            yield 'issue_' . $case->name => [$case];
        }

        foreach (TestOutcome::cases() as $case) {
            yield 'outcome_' . $case->name => [$case];
        }
    }

    #[DataProvider('completedOutcomeProvider')]
    public function testOnTestCompletedPurgesQueueWhenShouldStopOn(TestIssue|TestOutcome $outcome): void
    {
        $eventDispatcher = $this->prophesize(EventDispatcherInterface::class);
        $eventDispatcher->dispatch(Argument::cetera())
            ->shouldNotBeCalled();

        $testList = $this->prophesize(TestList::class);
        $testList->getTests()
            ->willReturn([]);

        $pipelineCollection = $this->prophesize(PipelineCollection::class);
        $pipelineCollection->hasEmptySlots()
            ->willReturn(true);
        $pipelineCollection->push(Argument::cetera())
            ->shouldNotBeCalled();

        $chunkSize = $this->prophesize(ChunkSize::class);
        $chunkSize->isChunked()
            ->willReturn(false);

        $chunkFile = $this->prophesize(ChunkFile::class);
        $chunkFile->createChunkFile()
            ->shouldNotBeCalled();

        $runnerConfiguration = $this->prophesize(RunnerConfiguration::class);
        $runnerConfiguration->shouldStopOn($outcome)
            ->willReturn(true);

        $runner = new Runner(
            $runnerConfiguration->reveal(),
            $eventDispatcher->reveal(),
            $this->mockProcessFactory(),
            $testList->reveal(),
            $pipelineCollection->reveal(),
            $chunkSize->reveal(),
            $chunkFile->reveal()
        );

        $runner->onProcessToBeRetried(new ProcessToBeRetried(new StubbedParaunitProcess()));
        $runner->onTestCompleted(new TestCompleted(new Test('FooTest'), $outcome));
        $runner->pushToPipeline();
    }

    #[DataProvider('completedOutcomeProvider')]
    public function testOnTestCompletedDoesNotPurgeWhenShouldStopOnFalse(TestIssue|TestOutcome $outcome): void
    {
        $eventDispatcher = $this->prophesize(EventDispatcherInterface::class);
        $eventDispatcher->dispatch(Argument::cetera())
            ->shouldNotBeCalled();

        $testList = $this->prophesize(TestList::class);
        $testList->getTests()
            ->willReturn([]);

        $pipelineCollection = $this->prophesize(PipelineCollection::class);
        $pipelineCollection->hasEmptySlots()
            ->willReturn(true);
        $pipelineCollection->push(Argument::cetera())
            ->shouldBeCalledTimes(2)
            ->willReturn($this->prophesize(Pipeline::class)->reveal());

        $chunkSize = $this->prophesize(ChunkSize::class);
        $chunkSize->isChunked()
            ->willReturn(false);

        $chunkFile = $this->prophesize(ChunkFile::class);
        $chunkFile->createChunkFile()
            ->shouldNotBeCalled();

        $runnerConfiguration = $this->prophesize(RunnerConfiguration::class);
        $runnerConfiguration->shouldStopOn($outcome)
            ->willReturn(false);

        $runner = new Runner(
            $runnerConfiguration->reveal(),
            $eventDispatcher->reveal(),
            $this->mockProcessFactory(),
            $testList->reveal(),
            $pipelineCollection->reveal(),
            $chunkSize->reveal(),
            $chunkFile->reveal()
        );

        $runner->onProcessToBeRetried(new ProcessToBeRetried(new StubbedParaunitProcess()));
        $runner->onProcessToBeRetried(new ProcessToBeRetried(new StubbedParaunitProcess()));
        $runner->onTestCompleted(new TestCompleted(new Test('FooTest'), $outcome));
        $runner->pushToPipeline();
    }

    public function testRunEmptyTestSuite(): void
    {
        $testList = $this->prophesize(TestList::class);
        $testList->getTests()
            ->willReturn([]);
        $pipelineCollection = $this->prophesize(PipelineCollection::class);
        $pipelineCollection->triggerProcessTermination()
            ->shouldBeCalled();
        $pipelineCollection->hasEmptySlots()
            ->willReturn(true);
        $pipelineCollection->isEmpty()
            ->willReturn(true);
        $chunkFile = $this->prophesize(ChunkFile::class);
        $chunkFile->createChunkFile()
            ->shouldNotBeCalled();

        $runner = new Runner(
            $this->mockRunnerConfiguration(),
            $this->mockEventDispatcher(),
            $this->mockProcessFactory(),
            $testList->reveal(),
            $pipelineCollection->reveal(),
            $this->mockChunkSize(false),
            $chunkFile->reveal()
        );

        $this->assertSame(0, $runner->run());
    }

    public function testRunWithSomeGreenTests(): void
    {
        $testList = $this->prophesize(TestList::class);
        $testList->getTests()
            ->willReturn([
                'Test1.php',
                'Test2.php',
            ]);
        $pipelineCollection = $this->prophesize(PipelineCollection::class);
        $pipelineCollection->triggerProcessTermination()
            ->shouldBeCalled();
        $pipelineCollection->hasEmptySlots()
            ->willReturn(true);
        $pipelineCollection->isEmpty()
            ->willReturn(true);
        $pipelineCollection->push(Argument::cetera())
            ->shouldBeCalledTimes(2)
            ->willReturn($this->prophesize(Pipeline::class)->reveal());
        $chunkFile = $this->prophesize(ChunkFile::class);
        $chunkFile->createChunkFile()
            ->shouldNotBeCalled();

        $runner = new Runner(
            $this->mockRunnerConfiguration(),
            $this->mockEventDispatcher(),
            $this->mockProcessFactory(),
            $testList->reveal(),
            $pipelineCollection->reveal(),
            $this->mockChunkSize(false),
            $chunkFile->reveal()
        );

        $this->assertSame(0, $runner->run());
    }

    public function testRunWithChunkedSomeGreenTests(): void
    {
        $testList = $this->prophesize(TestList::class);
        $testList->getTests()
            ->willReturn([
                'Test1.php',
                'Test2.php',
                'Test3.php',
            ]);
        $pipelineCollection = $this->prophesize(PipelineCollection::class);
        $pipelineCollection->triggerProcessTermination()
            ->shouldBeCalled();
        $pipelineCollection->hasEmptySlots()
            ->willReturn(true);
        $pipelineCollection->isEmpty()
            ->willReturn(true);
        $pipelineCollection->push(Argument::cetera())
            ->shouldBeCalledTimes(2)
            ->willReturn($this->prophesize(Pipeline::class)->reveal());
        $chunkFile = $this->prophesize(ChunkFile::class);
        $chunkFile->createChunkFile(0, ['Test1.php', 'Test2.php'])
            ->shouldBeCalled()
            ->willReturn('abcd_0.xml');
        $chunkFile->createChunkFile(1, ['Test3.php'])
            ->shouldBeCalled()
            ->willReturn('abcd_1.xml');

        $runner = new Runner(
            $this->mockRunnerConfiguration(),
            $this->mockEventDispatcher(),
            $this->mockProcessFactory('.xml'),
            $testList->reveal(),
            $pipelineCollection->reveal(),
            $this->mockChunkSize(true),
            $chunkFile->reveal()
        );

        $this->assertSame(0, $runner->run());
    }

    public function testOnProcessParsingCompletedWithFailedProcess(): void
    {
        $process = new StubbedParaunitProcess();
        $process->setIsToBeRetried(false);
        $process->exitCode = 1;

        $eventDispatcher = $this->prophesize(EventDispatcherInterface::class);
        $eventDispatcher->dispatch(Argument::cetera())
            ->shouldNotBeCalled();

        $testList = $this->prophesize(TestList::class);
        $testList->getTests()
            ->willReturn([]);
        $pipelineCollection = $this->prophesize(PipelineCollection::class);
        $pipelineCollection->push($process)
            ->shouldNotBeCalled();
        $chunkSize = $this->prophesize(ChunkSize::class);
        $chunkFile = $this->prophesize(ChunkFile::class);
        $chunkFile->createChunkFile()
            ->shouldNotBeCalled();

        $runner = new Runner(
            $this->mockRunnerConfiguration(),
            $eventDispatcher->reveal(),
            $this->mockProcessFactory(),
            $testList->reveal(),
            $pipelineCollection->reveal(),
            $chunkSize->reveal(),
            $chunkFile->reveal()
        );

        $runner->onProcessParsingCompleted(new ProcessParsingCompleted($process));
    }

    public function testOnProcessToBeRetried(): void
    {
        $process = new StubbedParaunitProcess();
        $process->setIsToBeRetried(true);
        $process->exitCode = 1;

        $eventDispatcher = $this->prophesize(EventDispatcherInterface::class);
        $eventDispatcher->dispatch(Argument::cetera())
            ->shouldNotBeCalled();

        $testList = $this->prophesize(TestList::class);
        $testList->getTests()
            ->willReturn([]);
        $pipelineCollection = $this->prophesize(PipelineCollection::class);
        $pipelineCollection->push($process)
            ->shouldNotBeCalled();
        $chunkSize = $this->prophesize(ChunkSize::class);
        $chunkFile = $this->prophesize(ChunkFile::class);
        $chunkFile->createChunkFile()
            ->shouldNotBeCalled();

        $runner = new Runner(
            $this->mockRunnerConfiguration(),
            $eventDispatcher->reveal(),
            $this->mockProcessFactory(),
            $testList->reveal(),
            $pipelineCollection->reveal(),
            $chunkSize->reveal(),
            $chunkFile->reveal()
        );

        $runner->onProcessToBeRetried(new ProcessToBeRetried($process));
    }

    public function testOnShutdownPurgesQueueInNonChunkedMode(): void
    {
        $eventDispatcher = $this->prophesize(EventDispatcherInterface::class);
        $eventDispatcher->dispatch(Argument::cetera())
            ->shouldNotBeCalled();

        $testList = $this->prophesize(TestList::class);
        $testList->getTests()
            ->willReturn([]);

        $pipelineCollection = $this->prophesize(PipelineCollection::class);
        $pipelineCollection->triggerProcessTermination()
            ->shouldBeCalledOnce();
        $pipelineCollection->hasEmptySlots()
            ->willReturn(true);
        $pipelineCollection->push(Argument::cetera())
            ->shouldNotBeCalled();

        $chunkSize = $this->prophesize(ChunkSize::class);
        $chunkSize->isChunked()
            ->willReturn(false);

        $chunkFile = $this->prophesize(ChunkFile::class);
        $chunkFile->createChunkFile(Argument::cetera())
            ->shouldNotBeCalled();

        $runner = new Runner(
            $this->mockRunnerConfiguration(),
            $eventDispatcher->reveal(),
            $this->mockProcessFactory(),
            $testList->reveal(),
            $pipelineCollection->reveal(),
            $chunkSize->reveal(),
            $chunkFile->reveal()
        );

        $runner->onProcessToBeRetried(new ProcessToBeRetried(new StubbedParaunitProcess('a.php')));
        $runner->onProcessToBeRetried(new ProcessToBeRetried(new StubbedParaunitProcess('b.php')));
        $runner->onShutdown();
        $runner->pushToPipeline();
    }

    public function testOnShutdownChunkedDeletesChunkFilesForRunningAndQueuedProcesses(): void
    {
        $eventDispatcher = $this->prophesize(EventDispatcherInterface::class);
        $eventDispatcher->dispatch(Argument::cetera())
            ->shouldNotBeCalled();

        $testList = $this->prophesize(TestList::class);
        $testList->getTests()
            ->willReturn([]);

        $running = new StubbedParaunitProcess('running.xml');
        $queuedA = new StubbedParaunitProcess('queued-a.xml');
        $queuedB = new StubbedParaunitProcess('queued-b.xml');

        $pipelineCollection = $this->prophesize(PipelineCollection::class);
        $pipelineCollection->triggerProcessTermination()
            ->shouldBeCalledOnce();
        $pipelineCollection->getRunningProcesses()
            ->shouldBeCalledOnce()
            ->willReturn([$running]);

        $chunkSize = $this->prophesize(ChunkSize::class);
        $chunkSize->isChunked()
            ->willReturn(true);

        $chunkFile = $this->prophesize(ChunkFile::class);
        $chunkFile->createChunkFile(Argument::cetera())
            ->shouldNotBeCalled();
        $chunkFile->deleteChunkFile(Argument::any())
            ->shouldBeCalledTimes(3);

        $runner = new Runner(
            $this->mockRunnerConfiguration(),
            $eventDispatcher->reveal(),
            $this->mockProcessFactory(),
            $testList->reveal(),
            $pipelineCollection->reveal(),
            $chunkSize->reveal(),
            $chunkFile->reveal()
        );

        $runner->onProcessToBeRetried(new ProcessToBeRetried($queuedA));
        $runner->onProcessToBeRetried(new ProcessToBeRetried($queuedB));
        $runner->onShutdown();
    }

    public function testOnShutdownChunkedWithEmptyQueue(): void
    {
        $eventDispatcher = $this->prophesize(EventDispatcherInterface::class);
        $eventDispatcher->dispatch(Argument::cetera())
            ->shouldNotBeCalled();

        $testList = $this->prophesize(TestList::class);
        $testList->getTests()
            ->willReturn([]);

        $running = new StubbedParaunitProcess('running.xml');

        $pipelineCollection = $this->prophesize(PipelineCollection::class);
        $pipelineCollection->triggerProcessTermination()
            ->shouldBeCalledOnce();
        $pipelineCollection->getRunningProcesses()
            ->shouldBeCalledOnce()
            ->willReturn([$running]);

        $chunkSize = $this->prophesize(ChunkSize::class);
        $chunkSize->isChunked()
            ->willReturn(true);

        $chunkFile = $this->prophesize(ChunkFile::class);
        $chunkFile->createChunkFile()
            ->shouldNotBeCalled();
        $chunkFile->deleteChunkFile($running)
            ->shouldBeCalledOnce();

        $runner = new Runner(
            $this->mockRunnerConfiguration(),
            $eventDispatcher->reveal(),
            $this->mockProcessFactory(),
            $testList->reveal(),
            $pipelineCollection->reveal(),
            $chunkSize->reveal(),
            $chunkFile->reveal()
        );

        $runner->onShutdown();
    }

    private function mockRunnerConfiguration(): RunnerConfiguration
    {
        $runnerConfiguration = $this->prophesize(RunnerConfiguration::class);
        $runnerConfiguration->shouldStopOn(Argument::any())
            ->willReturn(false);

        return $runnerConfiguration->reveal();
    }

    private function mockEventDispatcher(): EventDispatcherInterface
    {
        $eventDispatcher = $this->prophesize(EventDispatcherInterface::class);
        $eventDispatcher->dispatch(Argument::type(BeforeEngineStart::class))
            ->shouldBeCalledTimes(1)
            ->will(function ($args) use ($eventDispatcher) {
                $eventDispatcher->dispatch(Argument::type(EngineStart::class))
                    ->shouldBeCalledTimes(1)
                    ->will(function ($args) use ($eventDispatcher) {
                        $eventDispatcher->dispatch(Argument::type(EngineEnd::class))
                            ->shouldBeCalledTimes(1);

                        return $args[0];
                    });

                return $args[0];
            });

        return $eventDispatcher->reveal();
    }

    private function mockProcessFactory(string $ext = '.php'): ProcessFactory
    {
        $processFactory = $this->prophesize(ProcessFactory::class);
        $processFactory->create(Argument::containingString($ext))
            ->willReturn($this->prophesize(Process::class)->reveal());

        return $processFactory->reveal();
    }

    private function mockChunkSize(bool $enabled): ChunkSize
    {
        $chunkSize = $this->prophesize(ChunkSize::class);
        $chunkSize->isChunked()
            ->shouldBeCalled()
            ->willReturn($enabled);

        if ($enabled) {
            $chunkSize->getChunkSize()
                ->shouldBeCalled()
                ->willReturn(2);
        }

        return $chunkSize->reveal();
    }
}
