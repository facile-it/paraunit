<?php

namespace Tests\Unit\Runner;

use Paraunit\Lifecycle\ProcessEvent;
use Paraunit\Runner\Pipeline;
use Prophecy\Argument;
use Tests\BaseUnitTestCase;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Paraunit\Process\ParaunitProcessInterface;

/**
 * Class PipelineTest
 * @package Tests\Unit\Runner
 */
class PipelineTest extends BaseUnitTestCase
{
    public function testExecute()
    {
        $eventDispatcher = $this->prophesize(EventDispatcherInterface::class);
        $eventDispatcher->dispatch(Argument::cetera())
            ->shouldNotBeCalled();
        $process = $this->prophesize(ParaunitProcessInterface::class);
        $pipeline = new Pipeline($eventDispatcher->reveal(), 5);

        $pipeline->execute($process->reveal());

        $process->start(array(Pipeline::ENV_VAR_NAME_PIPELINE_NUMBER => 5))
            ->shouldHaveBeenCalledTimes(1);
    }

    public function testIsFree()
    {
        $eventDispatcher = $this->prophesize(EventDispatcherInterface::class);
        $eventDispatcher->dispatch(ProcessEvent::PROCESS_TERMINATED, Argument::cetera())
            ->shouldBeCalledTimes(1);
        $process = $this->prophesize(ParaunitProcessInterface::class);
        $process->start(array(Pipeline::ENV_VAR_NAME_PIPELINE_NUMBER => 5))
            ->shouldBeCalledTimes(1);
        $process->isTerminated()
            ->willReturn(true);
        $pipeline = new Pipeline($eventDispatcher->reveal(), 5);

        $this->assertTrue($pipeline->isFree(), 'Pipeline should be free to start with');
        $pipeline->execute($process->reveal());
        $this->assertFalse($pipeline->isFree(), 'Pipeline is marked free during execution of process');
        $this->assertTrue($pipeline->isTerminated(), 'I was expecting a termination of the process in the pipeline');
        $this->assertTrue($pipeline->isFree(), 'Pipeline is marked as not free after termination of process');
    }
}
