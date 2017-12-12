<?php

declare(strict_types=1);

namespace Paraunit\Printer;

use Paraunit\Lifecycle\ProcessEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Class DebugPrinter
 * @package Paraunit\Printer
 */
class DebugPrinter extends AbstractPrinter implements EventSubscriberInterface
{
    public static function getSubscribedEvents(): array
    {
        return [
            ProcessEvent::PROCESS_STARTED => 'onProcessStarted',
            ProcessEvent::PROCESS_TERMINATED => 'onProcessTerminated',
            ProcessEvent::PROCESS_PARSING_COMPLETED => ['onProcessCompleted', 1],
            ProcessEvent::PROCESS_TO_BE_RETRIED => 'onProcessToBeRetried',
        ];
    }

    public function onProcessStarted(ProcessEvent $event)
    {
        $process = $event->getProcess();

        $this->getOutput()->writeln('PROCESS STARTED: ' . $process->getFilename());
        $this->getOutput()->writeln($process->getCommandLine());
        $this->getOutput()->writeln('');
    }

    public function onProcessTerminated(ProcessEvent $event)
    {
        $process = $event->getProcess();

        $this->getOutput()->writeln('');
        $this->getOutput()->writeln('PROCESS TERMINATED: ' . $process->getFilename());
        $this->getOutput()->writeln(' - with class name: ' . $process->getTestClassName() ?? 'N/A');
        $this->getOutput()->writeln('');
    }

    public function onProcessParsingCompleted()
    {
        $this->getOutput()->write('PROCESS PARSING COMPLETED -- RESULTS: ');
    }

    public function onProcessToBeRetried(ProcessEvent $event)
    {
        $process = $event->getProcess();

        $this->getOutput()->writeln('');
        $this->getOutput()->writeln('PROCESS TO BE RETRIED: ' . $process->getFilename());
    }
}
