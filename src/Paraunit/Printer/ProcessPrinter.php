<?php

declare(strict_types=1);

namespace Paraunit\Printer;

use Paraunit\Lifecycle\EngineEvent;
use Paraunit\Lifecycle\ProcessEvent;
use Paraunit\TestResult\Interfaces\PrintableTestResultInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class ProcessPrinter implements EventSubscriberInterface
{
    public const MAX_CHAR_LENGTH = 80;

    private const COUNTER_CHAR_LENGTH = 5;

    /** @var SingleResultFormatter */
    private $singleResultFormatter;

    /** @var OutputInterface */
    private $output;

    /** @var int */
    private $counter;

    /** @var int */
    private $singleRowCounter;

    public function __construct(SingleResultFormatter $singleResultFormatter, OutputInterface $output)
    {
        $this->singleResultFormatter = $singleResultFormatter;
        $this->output = $output;
        $this->counter = 0;
        $this->singleRowCounter = 0;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            ProcessEvent::PROCESS_PARSING_COMPLETED => 'onProcessCompleted',
            ProcessEvent::PROCESS_TO_BE_RETRIED => 'onProcessCompleted',
            EngineEvent::END => ['onEngineEnd', 400],
        ];
    }

    public function onProcessCompleted(ProcessEvent $processEvent): void
    {
        $process = $processEvent->getProcess();

        foreach ($process->getTestResults() as $testResult) {
            $this->printFormattedWithCounter($testResult);
        }
    }

    public function onEngineEnd(): void
    {
        while (! $this->isRowFull()) {
            $this->output->write(' ');
            ++$this->singleRowCounter;
        }

        $this->printCounter();
    }

    private function printFormattedWithCounter(PrintableTestResultInterface $testResult): void
    {
        if ($this->isRowFull()) {
            $this->printCounter();
        }

        ++$this->counter;
        ++$this->singleRowCounter;

        $this->output->write(
            $this->singleResultFormatter->formatSingleResult($testResult)
        );
    }

    private function printCounter(): void
    {
        $this->output->writeln(sprintf('%6d', $this->counter));
        $this->singleRowCounter = 0;
    }

    private function isRowFull(): bool
    {
        return $this->singleRowCounter === self::MAX_CHAR_LENGTH - (self::COUNTER_CHAR_LENGTH + 1);
    }
}
