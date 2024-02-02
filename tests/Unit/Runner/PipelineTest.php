<?php

declare(strict_types=1);

namespace Tests\Unit\Runner;

use Paraunit\Lifecycle\ProcessStarted;
use Paraunit\Lifecycle\ProcessTerminated;
use Paraunit\Process\Process;
use Paraunit\Runner\Pipeline;
use Prophecy\Argument;
use Psr\EventDispatcher\EventDispatcherInterface;
use Tests\BaseUnitTestCase;

class PipelineTest extends BaseUnitTestCase
{
    public function testExecute(): void
    {
        $eventDispatcher = $this->prophesize(EventDispatcherInterface::class);
        $eventDispatcher->dispatch(Argument::type(ProcessStarted::class))
            ->shouldBeCalledTimes(1);
        $process = $this->prophesize(Process::class);
        $pipeline = new Pipeline($eventDispatcher->reveal(), 5);

        $pipeline->execute($process->reveal());

        $process->start(5)
            ->shouldHaveBeenCalledTimes(1);
    }

    public function testExecuteWithOccupiedPipeline(): void
    {
        $eventDispatcher = $this->prophesize(EventDispatcherInterface::class);
        $eventDispatcher->dispatch(Argument::type(ProcessStarted::class))
            ->shouldBeCalledTimes(1);
        $process = $this->prophesize(Process::class);
        $pipeline = new Pipeline($eventDispatcher->reveal(), 5);

        $pipeline->execute($process->reveal());

        $this->expectException(\RuntimeException::class);

        $pipeline->execute($process->reveal());
    }

    public function testIsFree(): void
    {
        $eventDispatcher = $this->prophesize(EventDispatcherInterface::class);
        $eventDispatcher->dispatch(Argument::type(ProcessTerminated::class))
            ->shouldNotBeCalled();

        $pipeline = new Pipeline($eventDispatcher->reveal(), 5);

        $this->assertTrue($pipeline->isFree(), 'Pipeline should be free to start with');
        $this->assertTrue($pipeline->isTerminated(), 'Pipeline should be considered terminated when empty');
    }

    public function testIsTerminated(): void
    {
        $eventDispatcher = $this->prophesize(EventDispatcherInterface::class);
        $eventDispatcher->dispatch(Argument::type(ProcessStarted::class))
            ->shouldBeCalledTimes(1);
        $process = $this->prophesize(Process::class);
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
        $eventDispatcher->dispatch(Argument::cetera())
            ->willReturnArgument(0);
        $process = $this->prophesize(Process::class);
        $process->start(5)
            ->shouldBeCalledTimes(1);
        $process->isTerminated()
            ->willReturn(true);
        $pipeline = new Pipeline($eventDispatcher->reveal(), 5);

        $this->assertTrue($pipeline->isFree(), 'Pipeline should be free to start with');

        $pipeline->execute($process->reveal());

        $this->assertFalse($pipeline->isFree(), 'Pipeline is marked free during execution of process');
        $this->assertTrue($pipeline->triggerTermination(), 'I was expecting a termination of the process in the pipeline');

        $eventDispatcher->dispatch(Argument::type(ProcessTerminated::class))
            ->shouldHaveBeenCalledTimes(1);

        $this->assertTrue($pipeline->isFree(), 'Pipeline is marked as not free after termination of process');
    }

    public function testIsTerminatedFalse(): void
    {
        $eventDispatcher = $this->prophesize(EventDispatcherInterface::class);
        $eventDispatcher->dispatch(Argument::type(ProcessStarted::class))
            ->shouldBeCalledTimes(1);
        $eventDispatcher->dispatch(Argument::type(ProcessTerminated::class))
            ->shouldNotBeCalled();
        $process = $this->prophesize(Process::class);
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
        $pipeline = new Pipeline($eventDispatcher->reveal(), 123_456);

        $this->assertSame(123_456, $pipeline->getNumber());
    }
}
