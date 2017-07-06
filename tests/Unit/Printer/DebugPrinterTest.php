<?php

namespace Tests\Unit\Printer;

use Paraunit\Lifecycle\ProcessEvent;
use Paraunit\Printer\DebugPrinter;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Tests\BaseUnitTestCase;
use Tests\Stub\StubbedParaunitProcess;
use Tests\Stub\UnformattedOutputStub;

class DebugPrinterTest extends BaseUnitTestCase
{
    public function testIsSubscribedToAllProcessEvents()
    {
        $this->assertTrue(
            is_subclass_of(DebugPrinter::class, EventSubscriberInterface::class),
            DebugPrinter::class . ' is not an EventSubscriber!'
        );

        $reflectionClass = new \ReflectionClass(ProcessEvent::class);
        $subscribedEvents = array_keys(DebugPrinter::getSubscribedEvents());

        foreach ($reflectionClass->getConstants() as $eventName) {
            $this->assertContains($eventName, $subscribedEvents, 'Not subscribed to event ' . $eventName);
        }
    }

    public function testOnProcessStarted()
    {
        $output = new UnformattedOutputStub();
        $printer = new DebugPrinter($output);
        $process = new StubbedParaunitProcess();
        
        $printer->onProcessStarted(new ProcessEvent($process));
        
        $this->assertContains('PROCESS STARTED', $output->getOutput());
        $this->assertContains($process->getFilename(), $output->getOutput());
        $this->assertContains($process->getCommandLine(), $output->getOutput());
    }

    public function testOnProcessTerminated()
    {
        $output = new UnformattedOutputStub();
        $printer = new DebugPrinter($output);
        $process = new StubbedParaunitProcess();
        $process->setTestClassName('Some\Class\Name');
        
        $printer->onProcessTerminated(new ProcessEvent($process));
        
        $this->assertContains('PROCESS TERMINATED', $output->getOutput());
        $this->assertContains($process->getFilename(), $output->getOutput());
        $this->assertContains($process->getTestClassName(), $output->getOutput());
    }

    public function testOnProcessParsingCompleted()
    {
        $output = new UnformattedOutputStub();
        $printer = new DebugPrinter($output);
        
        $printer->onProcessParsingCompleted();
        
        $this->assertContains('PROCESS PARSING COMPLETED', $output->getOutput());
        $this->assertContains('RESULTS', $output->getOutput());
    }

    public function testOnProcessToBeRetried()
    {
        $output = new UnformattedOutputStub();
        $printer = new DebugPrinter($output);
        $process = new StubbedParaunitProcess();

        $printer->onProcessToBeRetried(new ProcessEvent($process));
        
        $this->assertContains('PROCESS TO BE RETRIED', $output->getOutput());
        $this->assertContains($process->getFilename(), $output->getOutput());
    }
}
