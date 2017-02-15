<?php

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
            $this->prophesize('Paraunit\Runner\Pipeline')->reveal(),
            $this->prophesize('Paraunit\Runner\Pipeline')->reveal(),
            $this->prophesize('Paraunit\Runner\Pipeline')->reveal(),
            $this->prophesize('Paraunit\Runner\Pipeline')->reveal(),
            $this->prophesize('Paraunit\Runner\Pipeline')->reveal(),
        );

        new PipelineCollection($this->mockPipelineFactory($pipelines), count($pipelines));
    }

    public function testPush()
    {
        $pipeline = $this->prophesize('Paraunit\Runner\Pipeline');
        $pipeline->isFree()
            ->willReturn(false);
        $pipeline->isTerminated()
            ->shouldBeCalledTimes(3)
            ->willReturn(false, false, true);
        $pipeline->execute(Argument::cetera())
            ->shouldBeCalledTimes(1);

        $collection = new PipelineCollection($this->mockPipelineFactory(array($pipeline->reveal())), 1);

        $collection->push(new StubbedParaunitProcess());

        ClockMock::withClockMock(false);
    }

    public function testWaitCompletion()
    {
        $freePipeline = $this->prophesize('Paraunit\Runner\Pipeline');
        $freePipeline->isFree()
            ->shouldBeCalled()
            ->willReturn(true);
        $pipeline = $this->prophesize('Paraunit\Runner\Pipeline');
        $pipeline->isFree()
            ->willReturn(false);
        $pipeline->waitCompletion()
            ->willReturn(new StubbedParaunitProcess());

        $collection = new PipelineCollection($this->mockPipelineFactory(array($pipeline->reveal(), $freePipeline->reveal())), 2);

        $collection->waitForCompletion();
    }

    /**
     * @param array | Pipeline[] $pipelines
     * @return PipelineFactory
     */
    private function mockPipelineFactory(array $pipelines)
    {
        $factory = $this->prophesize('Paraunit\Runner\PipelineFactory');

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

        ClockMock::register('Paraunit\Runner\PipelineCollection');
        ClockMock::withClockMock(true);
    }

    protected function tearDown()
    {
        ClockMock::withClockMock(false);

        parent::tearDown();
    }
}
