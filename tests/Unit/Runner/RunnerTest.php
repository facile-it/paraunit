<?php

namespace Tests\Unit\Runner;

use Paraunit\Filter\Filter;
use Paraunit\Lifecycle\EngineEvent;
use Paraunit\Process\ProcessBuilderFactory;
use Paraunit\Runner\PipelineCollection;
use Paraunit\Runner\Runner;
use Prophecy\Argument;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Process\ProcessBuilder;
use Tests\BaseUnitTestCase;

class RunnerTest extends BaseUnitTestCase
{
    public function testRunEmptyTestSuite()
    {
        $filter = $this->prophesize(Filter::class);
        $filter->filterTestFiles()
            ->willReturn([]);
        $pipelineCollection = $this->prophesize(PipelineCollection::class);
        
        
        $runner = new Runner(
            $this->mockEventDispatcher(),
            $this->mockProcessBuilderFactory(),
            $filter->reveal(),
            $pipelineCollection->reveal()
        );

        $this->assertSame(0, $runner->run());
    }

    public function testRunWithSomeGreenTests()
    {
        $filter = $this->prophesize(Filter::class);
        $filter->filterTestFiles()
            ->willReturn([
                'Test1.php',
                'Test2.php',
            ]);
        $pipelineCollection = $this->prophesize(PipelineCollection::class);
        
        
        $runner = new Runner(
            $this->mockEventDispatcher(),
            $this->mockProcessBuilderFactory(),
            $filter->reveal(),
            $pipelineCollection->reveal()
        );

        $this->assertSame(0, $runner->run());
    }

    public function testRunWithAFailingTest()
    {
        $this->markTestIncomplete();
    }

    private function mockEventDispatcher(): EventDispatcherInterface
    {
        $eventDispatcher = $this->prophesize(EventDispatcherInterface::class);
        $eventDispatcher->dispatch(EngineEvent::BEFORE_START, Argument::cetera())
            ->shouldBeCalledTimes(1)
            ->will(function () use ($eventDispatcher) {
                $eventDispatcher->dispatch(EngineEvent::START, Argument::cetera())
                    ->shouldBeCalledTimes(1)
                    ->will(function () use ($eventDispatcher) {
                        $eventDispatcher->dispatch(EngineEvent::END, Argument::cetera())
                            ->shouldBeCalledTimes(1);
                    });
            });

        return $eventDispatcher->reveal();
    }

    private function mockProcessBuilderFactory(): ProcessBuilderFactory
    {
        $processBuilderFactory = $this->prophesize(ProcessBuilderFactory::class);
        $processBuilderFactory->create(Argument::containingString('.php'))
            ->willReturn($this->mockProcessBuilder());

        return $processBuilderFactory->reveal();
    }

    private function mockProcessBuilder(): ProcessBuilder
    {
        $processBuilder = $this->prophesize(ProcessBuilder::class);
        
        return $processBuilder->reveal();
    }
}
