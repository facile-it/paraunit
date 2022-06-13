<?php

declare(strict_types=1);

namespace Paraunit\Printer;

use Paraunit\Lifecycle\AbstractEvent;
use Paraunit\Lifecycle\AbstractProcessEvent;
use Paraunit\Lifecycle\EngineEnd;
use Paraunit\Lifecycle\ProcessParsingCompleted;
use Paraunit\Lifecycle\ProcessToBeRetried;
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

    /**
     * @return array<class-string<AbstractEvent>, string|array{0: string, 1: int}>
     */
    public static function getSubscribedEvents(): array
    {
        return [
            ProcessParsingCompleted::class => 'onProcessCompleted',
            ProcessToBeRetried::class => 'onProcessCompleted',
            EngineEnd::class => ['onEngineEnd', 400],
        ];
    }

    public function onProcessCompleted(AbstractProcessEvent $processEvent): void
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
