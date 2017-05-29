<?php
declare(strict_types=1);

namespace Tests\Unit\Runner;

use Paraunit\Runner\Pipeline;
use Paraunit\Runner\PipelineCollection;
use Paraunit\Runner\PipelineFactory;
use Prophecy\Argument;
use Symfony\Bridge\PhpUnit\ClockMock;
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
        $pipeline = $this->prophesize(Pipeline::class);
        $pipeline->isFree()
            ->willReturn(false);
        $pipeline->isTerminated()
            ->shouldBeCalledTimes(3)
            ->willReturn(false, false, true);
        $pipeline->execute(Argument::cetera())
            ->shouldBeCalledTimes(1);

        $collection = new PipelineCollection($this->mockPipelineFactory([$pipeline->reveal()]), 1);

        $collection->push(new StubbedParaunitProcess());

        ClockMock::withClockMock(false);
    }

    public function testWaitCompletion()
    {
        $freePipeline = $this->prophesize(Pipeline::class);
        $freePipeline->isFree()
            ->shouldBeCalled()
            ->willReturn(true);
        $pipeline = $this->prophesize(Pipeline::class);
        $pipeline->isFree()
            ->willReturn(false);
        $pipeline->waitCompletion()
            ->willReturn(new StubbedParaunitProcess());

        $collection = new PipelineCollection(
            $this->mockPipelineFactory([$pipeline->reveal(), $freePipeline->reveal()]),
            2
        );

        $collection->waitForCompletion();
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

    protected function setUp()
    {
        parent::setUp();

        ClockMock::register(PipelineCollection::class);
        ClockMock::withClockMock(true);
    }

    protected function tearDown()
    {
        ClockMock::withClockMock(false);

        parent::tearDown();
    }
}
