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
use Paraunit\Process\Process;
use Paraunit\Process\ProcessFactory;
use Paraunit\Runner\ChunkFile;
use Paraunit\Runner\Pipeline;
use Paraunit\Runner\PipelineCollection;
use Paraunit\Runner\Runner;
use Prophecy\Argument;
use Psr\EventDispatcher\EventDispatcherInterface;
use Tests\BaseUnitTestCase;
use Tests\Stub\StubbedParaunitProcess;

class RunnerTest extends BaseUnitTestCase
{
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
            $eventDispatcher->reveal(),
            $this->mockProcessFactory(),
            $testList->reveal(),
            $pipelineCollection->reveal(),
            $chunkSize->reveal(),
            $chunkFile->reveal()
        );

        $runner->onProcessToBeRetried(new ProcessToBeRetried($process));
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
