<?php

namespace Paraunit\Printer;

use Paraunit\Lifecycle\ProcessEvent;
use Paraunit\TestResult\TestResultInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class ProcessPrinter
 * @package Paraunit\Printer
 */
class ProcessPrinter
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
     */
    public function __construct(SingleResultFormatter $singleResultFormatter)
    {
        $this->singleResultFormatter = $singleResultFormatter;
    }

    /**
     * @param ProcessEvent $processEvent
     */
    public function onProcessTerminated(ProcessEvent $processEvent)
    {
        $process = $processEvent->getProcess();

        if ( ! $processEvent->has('output_interface')) {
            throw new \BadMethodCallException('missing output_interface');
        }

        $this->output = $processEvent->get('output_interface');

        if ( ! $this->output instanceof OutputInterface) {
            throw new \BadMethodCallException('output_interface, unexpected type: ' . get_class($this->output));
        }

        foreach ($process->getTestResults() as $testResult) {
            $this->printFormattedWithCounter($testResult);
        }
    }

    /**
     * @param TestResultInterface $testResult
     */
    private function printFormattedWithCounter(TestResultInterface $testResult)
    {
        if ($this->counter % 80 == 0 && $this->counter > 1) {
            $this->output->writeln('');
        }

        ++$this->counter;

        $this->output->write(
            $this->singleResultFormatter->formatSingleResult($testResult)
        );
    }
}
