<?php

declare(strict_types=1);

namespace Paraunit\Printer;

use Paraunit\Lifecycle\AbstractEvent;
use Paraunit\Lifecycle\ProcessParsingCompleted;
use Paraunit\Lifecycle\ProcessStarted;
use Paraunit\Lifecycle\ProcessTerminated;
use Paraunit\Lifecycle\ProcessToBeRetried;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class DebugPrinter implements EventSubscriberInterface
{
    public function __construct(private readonly OutputInterface $output)
    {
    }

    /**
     * @return array<class-string<AbstractEvent>, string|array{0: string, 1: int}>
     */
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

        $this->output->writeln('PROCESS STARTED: ' . $process->getFilename());
        $this->output->writeln($process->getCommandLine());
        $this->output->writeln('');
    }

    public function onProcessTerminated(ProcessTerminated $event): void
    {
        $process = $event->getProcess();

        $this->output->writeln('');
        $this->output->writeln('PROCESS TERMINATED: ' . $process->getFilename());
        $this->output->writeln(' - with class name: ' . ($process->getTestClassName() ?? 'N/A'));
        $this->output->writeln('');
        $this->output->writeln('PROCESS FULL OUTPUT:');
        $this->output->writeln('');
        $this->output->writeln($process->getOutput());
    }

    public function onProcessParsingCompleted(): void
    {
        $this->output->write('PROCESS PARSING COMPLETED -- RESULTS: ');
    }

    public function onProcessToBeRetried(ProcessToBeRetried $event): void
    {
        $process = $event->getProcess();

        $this->output->writeln('');
        $this->output->writeln('PROCESS TO BE RETRIED: ' . $process->getFilename());
    }
}
