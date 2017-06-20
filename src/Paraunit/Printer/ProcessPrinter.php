<?php
declare(strict_types=1);

namespace Paraunit\Printer;

use Paraunit\Lifecycle\ProcessEvent;
use Paraunit\TestResult\Interfaces\PrintableTestResultInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Class ProcessPrinter
 * @package Paraunit\Printer
 */
class ProcessPrinter implements EventSubscriberInterface
{
    /** @var  SingleResultFormatter */
    private $singleResultFormatter;

    /** @var  OutputInterface */
    private $output;

    /** @var int */
    private $counter = 0;

    /**
     * ProcessPrinter constructor.
     * @param SingleResultFormatter $singleResultFormatter
     * @param OutputInterface $output
     */
    public function __construct(SingleResultFormatter $singleResultFormatter, OutputInterface $output)
    {
        $this->singleResultFormatter = $singleResultFormatter;
        $this->output = $output;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            ProcessEvent::PROCESS_PARSING_COMPLETED => 'onProcessParsingCompleted',
        ];
    }

    /**
     * @param ProcessEvent $processEvent
     * @throws \BadMethodCallException
     */
    public function onProcessParsingCompleted(ProcessEvent $processEvent)
    {
        $process = $processEvent->getProcess();

        foreach ($process->getTestResults() as $testResult) {
            $this->printFormattedWithCounter($testResult);
        }
    }

    /**
     * @param PrintableTestResultInterface $testResult
     */
    private function printFormattedWithCounter(PrintableTestResultInterface $testResult)
    {
        if ($this->counter % 80 === 0 && $this->counter > 1) {
            $this->output->writeln('');
        }

        ++$this->counter;

        $this->output->write(
            $this->singleResultFormatter->formatSingleResult($testResult)
        );
    }
}
