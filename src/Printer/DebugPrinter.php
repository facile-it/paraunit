<?php

declare(strict_types=1);

namespace Paraunit\Printer;

use Paraunit\Lifecycle\ProcessParsingCompleted;
use Paraunit\Lifecycle\ProcessStarted;
use Paraunit\Lifecycle\ProcessTerminated;
use Paraunit\Lifecycle\ProcessToBeRetried;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class DebugPrinter extends AbstractPrinter implements EventSubscriberInterface
{
    public static function getSubscribedEvents(): array
    {
        return [
            ProcessStarted::class => 'onProcessStarted',
            ProcessTerminated::class => 'onProcessTerminated',
            ProcessParsingCompleted::class => ['onProcessParsingCompleted', 1],
            ProcessToBeRetried::class => 'onProcessToBeRetried',
        ];
    }

    public function onProcessStarted(ProcessStarted $event): void
    {
        $process = $event->getProcess();

        $this->getOutput()->writeln('PROCESS STARTED: ' . $process->getFilename());
        $this->getOutput()->writeln($process->getCommandLine());
        $this->getOutput()->writeln('');
    }

    public function onProcessTerminated(ProcessTerminated $event): void
    {
        $process = $event->getProcess();

        $this->getOutput()->writeln('');
        $this->getOutput()->writeln('PROCESS TERMINATED: ' . $process->getFilename());
        $this->getOutput()->writeln(' - with class name: ' . $process->getTestClassName() ?? 'N/A');
        $this->getOutput()->writeln('');
    }

    public function onProcessParsingCompleted(): void
    {
        $this->getOutput()->write('PROCESS PARSING COMPLETED -- RESULTS: ');
    }

    public function onProcessToBeRetried(ProcessToBeRetried $event): void
    {
        $process = $event->getProcess();

        $this->getOutput()->writeln('');
        $this->getOutput()->writeln('PROCESS TO BE RETRIED: ' . $process->getFilename());
    }
}
