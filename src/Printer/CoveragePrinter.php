<?php

declare(strict_types=1);

namespace Paraunit\Printer;

use Paraunit\Coverage\CoverageDriver;
use Paraunit\Lifecycle\AbstractEvent;
use Paraunit\Lifecycle\BeforeEngineStart;
use Paraunit\Process\CommandLineWithCoverage;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class CoveragePrinter implements EventSubscriberInterface
{
    public function __construct(
        private readonly CommandLineWithCoverage $commandLine,
        private readonly OutputInterface $output
    ) {
    }

    /**
     * @return array<class-string<AbstractEvent>, string|array{0: string, 1: int}>
     */
    public static function getSubscribedEvents(): array
    {
        return [
            BeforeEngineStart::class => ['onEngineBeforeStart', 100],
        ];
    }

    public function onEngineBeforeStart(): void
    {
        $driver = match ($this->commandLine->getCoverageDriver()) {
            CoverageDriver::Xdebug => 'Xdebug',
            CoverageDriver::Pcov => 'Pcov',
            CoverageDriver::PHPDbg => 'PHPDBG',
        };

        $this->output->write('Coverage driver in use: ' . $driver);
    }
}
