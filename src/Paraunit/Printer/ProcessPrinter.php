<?php

declare(strict_types=1);

namespace Paraunit\Printer;

use Paraunit\Lifecycle\EngineEvent;
use Paraunit\Lifecycle\ProcessEvent;
use Paraunit\TestResult\Interfaces\PrintableTestResultInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Class ProcessPrinter
 */
class ProcessPrinter implements EventSubscriberInterface
{
    const MAX_CHAR_LENGTH = 80;

    const COUNTER_CHAR_LENGTH = 5;

    /** @var SingleResultFormatter */
    private $singleResultFormatter;

    /** @var OutputInterface */
    private $output;

    /** @var int */
    private $counter;

    /** @var int */
    private $singleRowCounter;

    /**
     * ProcessPrinter constructor.
     *
     * @param SingleResultFormatter $singleResultFormatter
     * @param OutputInterface $output
     */
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

    /**
     * @param ProcessEvent $processEvent
     *
     * @throws \BadMethodCallException
     */
    public function onProcessCompleted(ProcessEvent $processEvent)
    {
        $process = $processEvent->getProcess();

        foreach ($process->getTestResults() as $testResult) {
            $this->printFormattedWithCounter($testResult);
        }
    }

    public function onEngineEnd()
    {
        while (! $this->isRowFull()) {
            $this->output->write(' ');
            ++$this->singleRowCounter;
        }

        $this->printCounter();
    }

    /**
     * @param PrintableTestResultInterface $testResult
     */
    private function printFormattedWithCounter(PrintableTestResultInterface $testResult)
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

    private function printCounter()
    {
        $this->output->writeln(sprintf('%6d', $this->counter));
        $this->singleRowCounter = 0;
    }

    private function isRowFull(): bool
    {
        return $this->singleRowCounter === self::MAX_CHAR_LENGTH - (self::COUNTER_CHAR_LENGTH + 1);
    }
}
