<?php

declare(strict_types=1);

namespace Tests\Unit\Printer;

use Paraunit\Lifecycle\ProcessParsingCompleted;
use Paraunit\Lifecycle\ProcessStarted;
use Paraunit\Lifecycle\ProcessTerminated;
use Paraunit\Lifecycle\ProcessToBeRetried;
use Paraunit\Printer\DebugPrinter;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Tests\BaseUnitTestCase;
use Tests\Stub\StubbedParaunitProcess;
use Tests\Stub\UnformattedOutputStub;

class DebugPrinterTest extends BaseUnitTestCase
{
    public function testIsSubscribedToAllProcessEvents(): void
    {
        $this->assertTrue(
            is_subclass_of(DebugPrinter::class, EventSubscriberInterface::class),
            DebugPrinter::class . ' is not an EventSubscriber!'
        );

        $subscribedEvents = array_keys(DebugPrinter::getSubscribedEvents());
        $processEvents = [
            ProcessParsingCompleted::class,
            ProcessStarted::class,
            ProcessTerminated::class,
            ProcessToBeRetried::class,
        ];

        foreach ($processEvents as $eventName) {
            $this->assertContains($eventName, $subscribedEvents, 'Not subscribed to event ' . $eventName);
        }
    }

    public function testOnProcessStarted(): void
    {
        $output = new UnformattedOutputStub();
        $printer = new DebugPrinter($output);
        $process = new StubbedParaunitProcess();

        $printer->onProcessStarted(new ProcessStarted($process));

        $this->assertContains('PROCESS STARTED', $output->getOutput());
        $this->assertContains($process->getFilename(), $output->getOutput());
        $this->assertContains($process->getCommandLine(), $output->getOutput());
    }

    public function testOnProcessTerminated(): void
    {
        $output = new UnformattedOutputStub();
        $printer = new DebugPrinter($output);
        $process = new StubbedParaunitProcess();
        $process->setTestClassName('Some\Class\Name');

        $printer->onProcessTerminated(new ProcessTerminated($process));

        $this->assertContains('PROCESS TERMINATED', $output->getOutput());
        $this->assertContains($process->getFilename(), $output->getOutput());
        $this->assertContains($process->getTestClassName(), $output->getOutput());
    }

    public function testOnProcessParsingCompleted(): void
    {
        $output = new UnformattedOutputStub();
        $printer = new DebugPrinter($output);

        $printer->onProcessParsingCompleted();

        $this->assertContains('PROCESS PARSING COMPLETED', $output->getOutput());
        $this->assertContains('RESULTS', $output->getOutput());
    }

    public function testOnProcessToBeRetried(): void
    {
        $output = new UnformattedOutputStub();
        $printer = new DebugPrinter($output);
        $process = new StubbedParaunitProcess();

        $printer->onProcessToBeRetried(new ProcessToBeRetried($process));

        $this->assertContains('PROCESS TO BE RETRIED', $output->getOutput());
        $this->assertContains($process->getFilename(), $output->getOutput());
    }
}
