<?php

declare(strict_types=1);

namespace Tests\Unit\Runner;

use Paraunit\Lifecycle\ProcessEvent;
use Paraunit\Process\AbstractParaunitProcess;
use Paraunit\Runner\Pipeline;
use Prophecy\Argument;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Tests\BaseUnitTestCase;

class PipelineTest extends BaseUnitTestCase
{
    public function testExecute(): void
    {
        $eventDispatcher = $this->prophesize(EventDispatcherInterface::class);
        $eventDispatcher->dispatch(ProcessEvent::PROCESS_STARTED, Argument::type(ProcessEvent::class))
            ->shouldBeCalledTimes(1);
        $process = $this->prophesize(AbstractParaunitProcess::class);
        $pipeline = new Pipeline($eventDispatcher->reveal(), 5);

        $pipeline->execute($process->reveal());

        $process->start(5)
            ->shouldHaveBeenCalledTimes(1);
    }

    public function testExecuteWithOccupiedPipeline(): void
    {
        $eventDispatcher = $this->prophesize(EventDispatcherInterface::class);
        $eventDispatcher->dispatch(ProcessEvent::PROCESS_STARTED, Argument::type(ProcessEvent::class))
            ->shouldBeCalledTimes(1);
        $process = $this->prophesize(AbstractParaunitProcess::class);
        $pipeline = new Pipeline($eventDispatcher->reveal(), 5);

        $pipeline->execute($process->reveal());

        $this->expectException(\RuntimeException::class);

        $pipeline->execute($process->reveal());
    }

    public function testIsFree(): void
    {
        $eventDispatcher = $this->prophesize(EventDispatcherInterface::class);
        $eventDispatcher->dispatch(ProcessEvent::PROCESS_TERMINATED, Argument::cetera())
            ->shouldNotBeCalled();

        $pipeline = new Pipeline($eventDispatcher->reveal(), 5);

        $this->assertTrue($pipeline->isFree(), 'Pipeline should be free to start with');
        $this->assertTrue($pipeline->isTerminated(), 'Pipeline should be considered terminated when empty');
    }

    public function testIsTerminated(): void
    {
        $eventDispatcher = $this->prophesize(EventDispatcherInterface::class);
        $eventDispatcher->dispatch(ProcessEvent::PROCESS_STARTED, Argument::type(ProcessEvent::class))
            ->shouldBeCalledTimes(1);
        $process = $this->prophesize(AbstractParaunitProcess::class);
        $process->start(5)
            ->shouldBeCalledTimes(1);
        $process->isTerminated()
            ->willReturn(true);
        $pipeline = new Pipeline($eventDispatcher->reveal(), 5);

        $this->assertTrue($pipeline->isFree(), 'Pipeline should be free to start with');

        $pipeline->execute($process->reveal());

        $this->assertFalse($pipeline->isFree(), 'Pipeline is marked free during execution of process');
        $this->assertTrue($pipeline->isTerminated(), 'I was expecting a termination of the process in the pipeline');
        $this->assertFalse($pipeline->isFree(), 'Pipeline is being freed');
    }

    public function testTriggerTermination(): void
    {
        $eventDispatcher = $this->prophesize(EventDispatcherInterface::class);
        $process = $this->prophesize(AbstractParaunitProcess::class);
        $process->start(5)
            ->shouldBeCalledTimes(1);
        $process->isTerminated()
            ->willReturn(true);
        $pipeline = new Pipeline($eventDispatcher->reveal(), 5);

        $this->assertTrue($pipeline->isFree(), 'Pipeline should be free to start with');

        $pipeline->execute($process->reveal());

        $this->assertFalse($pipeline->isFree(), 'Pipeline is marked free during execution of process');
        $this->assertTrue($pipeline->triggerTermination(), 'I was expecting a termination of the process in the pipeline');

        $eventDispatcher->dispatch(ProcessEvent::PROCESS_TERMINATED, Argument::cetera())
            ->shouldHaveBeenCalledTimes(1);

        $this->assertTrue($pipeline->isFree(), 'Pipeline is marked as not free after termination of process');
    }

    public function testIsTerminatedFalse(): void
    {
        $eventDispatcher = $this->prophesize(EventDispatcherInterface::class);
        $eventDispatcher->dispatch(ProcessEvent::PROCESS_STARTED, Argument::type(ProcessEvent::class))
            ->shouldBeCalledTimes(1);
        $eventDispatcher->dispatch(ProcessEvent::PROCESS_TERMINATED, Argument::cetera())
            ->shouldNotBeCalled();
        $process = $this->prophesize(AbstractParaunitProcess::class);
        $process->start(5)
            ->shouldBeCalledTimes(1);
        $process->isTerminated()
            ->willReturn(false);
        $pipeline = new Pipeline($eventDispatcher->reveal(), 5);

        $this->assertTrue($pipeline->isFree(), 'Pipeline should be free to start with');

        $pipeline->execute($process->reveal());

        $this->assertFalse($pipeline->isFree(), 'Pipeline is marked free during execution of process');
        $this->assertFalse($pipeline->isTerminated(), 'Process should not be terminated');
    }

    public function testGetNumber(): void
    {
        $eventDispatcher = $this->prophesize(EventDispatcherInterface::class);
        $pipeline = new Pipeline($eventDispatcher->reveal(), 123456);

        $this->assertSame(123456, $pipeline->getNumber());
    }
}
