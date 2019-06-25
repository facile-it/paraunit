<?php

declare(strict_types=1);

namespace Tests\Unit\Runner;

use Paraunit\Filter\Filter;
use Paraunit\Lifecycle\BeforeEngineStart;
use Paraunit\Lifecycle\EngineEnd;
use Paraunit\Lifecycle\EngineStart;
use Paraunit\Lifecycle\ProcessParsingCompleted;
use Paraunit\Lifecycle\ProcessToBeRetried;
use Paraunit\Process\AbstractParaunitProcess;
use Paraunit\Process\ProcessFactoryInterface;
use Paraunit\Runner\Pipeline;
use Paraunit\Runner\PipelineCollection;
use Paraunit\Runner\Runner;
use Prophecy\Argument;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Tests\BaseUnitTestCase;
use Tests\Stub\StubbedParaunitProcess;

class RunnerTest extends BaseUnitTestCase
{
    public function testRunEmptyTestSuite(): void
    {
        $filter = $this->prophesize(Filter::class);
        $filter->filterTestFiles()
            ->willReturn([]);
        $pipelineCollection = $this->prophesize(PipelineCollection::class);
        $pipelineCollection->triggerProcessTermination()
            ->shouldBeCalled();
        $pipelineCollection->hasEmptySlots()
            ->willReturn(true);
        $pipelineCollection->isEmpty()
            ->willReturn(true);

        $runner = new Runner(
            $this->mockEventDispatcher(),
            $this->mockProcessFactory(),
            $filter->reveal(),
            $pipelineCollection->reveal()
        );

        $this->assertSame(0, $runner->run());
    }

    public function testRunWithSomeGreenTests(): void
    {
        $filter = $this->prophesize(Filter::class);
        $filter->filterTestFiles()
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

        $runner = new Runner(
            $this->mockEventDispatcher(),
            $this->mockProcessFactory(),
            $filter->reveal(),
            $pipelineCollection->reveal()
        );

        $this->assertSame(0, $runner->run());
    }

    public function testOnProcessParsingCompletedWithFailedProcess(): void
    {
        $process = new StubbedParaunitProcess();
        $process->setIsToBeRetried(false);
        $process->setExitCode(1);

        $eventDispatcher = $this->prophesize(EventDispatcherInterface::class);
        $eventDispatcher->dispatch(Argument::cetera())
            ->shouldNotBeCalled();

        $filter = $this->prophesize(Filter::class);
        $filter->filterTestFiles()
            ->willReturn([]);
        $pipelineCollection = $this->prophesize(PipelineCollection::class);
        $pipelineCollection->push($process)
            ->shouldNotBeCalled();

        $runner = new Runner(
            $eventDispatcher->reveal(),
            $this->mockProcessFactory(),
            $filter->reveal(),
            $pipelineCollection->reveal()
        );

        $runner->onProcessParsingCompleted(new ProcessParsingCompleted($process));
    }

    public function testOnProcessToBeRetried(): void
    {
        $process = new StubbedParaunitProcess();
        $process->setIsToBeRetried(true);
        $process->setExitCode(1);

        $eventDispatcher = $this->prophesize(EventDispatcherInterface::class);
        $eventDispatcher->dispatch(Argument::cetera())
            ->shouldNotBeCalled();

        $filter = $this->prophesize(Filter::class);
        $filter->filterTestFiles()
            ->willReturn([]);
        $pipelineCollection = $this->prophesize(PipelineCollection::class);
        $pipelineCollection->push($process)
            ->shouldNotBeCalled();

        $runner = new Runner(
            $eventDispatcher->reveal(),
            $this->mockProcessFactory(),
            $filter->reveal(),
            $pipelineCollection->reveal()
        );

        $runner->onProcessToBeRetried(new ProcessToBeRetried($process));
    }

    private function mockEventDispatcher(): EventDispatcherInterface
    {
        $eventDispatcher = $this->prophesize(EventDispatcherInterface::class);
        $eventDispatcher->dispatch(Argument::type(BeforeEngineStart::class))
            ->shouldBeCalledTimes(1)
            ->will(function () use ($eventDispatcher) {
                $eventDispatcher->dispatch(Argument::type(EngineStart::class))
                    ->shouldBeCalledTimes(1)
                    ->will(function () use ($eventDispatcher) {
                        $eventDispatcher->dispatch(Argument::type(EngineEnd::class))
                            ->shouldBeCalledTimes(1);
                    });
            });

        return $eventDispatcher->reveal();
    }

    private function mockProcessFactory(): ProcessFactoryInterface
    {
        $processFactory = $this->prophesize(ProcessFactoryInterface::class);
        $processFactory->create(Argument::containingString('.php'))
            ->willReturn($this->prophesize(AbstractParaunitProcess::class)->reveal());

        return $processFactory->reveal();
    }
}
