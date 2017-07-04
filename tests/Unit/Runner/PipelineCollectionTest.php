<?php
declare(strict_types=1);

namespace Tests\Unit\Runner;

use Paraunit\Runner\Pipeline;
use Paraunit\Runner\PipelineCollection;
use Paraunit\Runner\PipelineFactory;
use Prophecy\Argument;
use Tests\BaseUnitTestCase;
use Tests\Stub\StubbedParaunitProcess;

/**
 * Class PipelineCollectionTest
 * @package Tests\Unit\Runner
 * @small
 */
class PipelineCollectionTest extends BaseUnitTestCase
{
    public function testInstantiation()
    {
        $pipelines = array(
            $this->prophesize(Pipeline::class)->reveal(),
            $this->prophesize(Pipeline::class)->reveal(),
            $this->prophesize(Pipeline::class)->reveal(),
            $this->prophesize(Pipeline::class)->reveal(),
            $this->prophesize(Pipeline::class)->reveal(),
        );

        new PipelineCollection($this->mockPipelineFactory($pipelines), count($pipelines));
    }

    public function testPush()
    {
        $newProcess = new StubbedParaunitProcess();

        $occupiedPipeline = $this->prophesize(Pipeline::class);
        $occupiedPipeline->isFree()
            ->willReturn(false);
        $occupiedPipeline->execute(Argument::cetera())
            ->shouldNotBeCalled();

        $freePipeline = $this->prophesize(Pipeline::class);
        $freePipeline->isFree()
            ->willReturn(true);
        $freePipeline->execute($newProcess)
            ->shouldBeCalledTimes(1);

        $collection = new PipelineCollection(
            $this->mockPipelineFactory([$occupiedPipeline->reveal(), $freePipeline->reveal()]),
            2
        );

        $collection->push($newProcess);
    }

    public function testPushWithNoEmptyPipelines()
    {
        $newProcess = new StubbedParaunitProcess();

        $occupiedPipeline = $this->prophesize(Pipeline::class);
        $occupiedPipeline->isFree()
            ->willReturn(false);
        $occupiedPipeline->execute(Argument::cetera())
            ->shouldNotBeCalled();

        $collection = new PipelineCollection(
            $this->mockPipelineFactory([$occupiedPipeline->reveal()]),
            1
        );

        $this->expectException(\RuntimeException::class);

        $collection->push($newProcess);
    }

    /**
     * @dataProvider pipelineStateProvider
     * @param bool $isPipeline1Terminated
     * @param bool $isPipeline2Terminated
     */
    public function testHasRunningProcesses(bool $isPipeline1Terminated, bool $isPipeline2Terminated)
    {
        $pipeline1 = $this->prophesize(Pipeline::class);
        $pipeline1->isTerminated()
            ->willReturn($isPipeline1Terminated);
        $pipeline2 = $this->prophesize(Pipeline::class);
        $pipeline2->isTerminated()
            ->willReturn($isPipeline2Terminated);

        $pipelineCollection = new PipelineCollection(
            $this->mockPipelineFactory([$pipeline1->reveal(), $pipeline2->reveal()]),
            2
        );

        $expectedResult = ! ($isPipeline1Terminated && $isPipeline2Terminated);
        $this->assertSame($expectedResult, $pipelineCollection->isEmpty());
    }

    /**
     * @dataProvider pipelineStateProvider
     * @param bool $isPipeline1Empty
     * @param bool $isPipeline2Empty
     */
    public function testHasEmptySlots(bool $isPipeline1Empty, bool $isPipeline2Empty)
    {
        $pipeline1 = $this->prophesize(Pipeline::class);
        $pipeline1->isFree()
            ->willReturn($isPipeline1Empty);
        $pipeline2 = $this->prophesize(Pipeline::class);
        $pipeline2->isFree()
            ->willReturn($isPipeline2Empty);

        $pipelineCollection = new PipelineCollection(
            $this->mockPipelineFactory([$pipeline1->reveal(), $pipeline2->reveal()]),
            2
        );

        $this->assertSame($isPipeline1Empty || $isPipeline2Empty, $pipelineCollection->hasEmptySlots());
    }

    public function pipelineStateProvider(): array
    {
        return [
            [false, false],
            [false, true],
            [true, false],
            [true, true],
        ];
    }

    /**
     * @param Pipeline[] $pipelines
     * @return PipelineFactory
     */
    private function mockPipelineFactory(array $pipelines): PipelineFactory
    {
        $factory = $this->prophesize(PipelineFactory::class);

        foreach ($pipelines as $number => $pipeline) {
            $factory->create($number)
                ->shouldBeCalledTimes(1)
                ->willReturn($pipeline);
        }

        return $factory->reveal();
    }
}
