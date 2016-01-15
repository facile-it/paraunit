<?php

namespace Paraunit\Printer;

use Paraunit\Lifecycle\ProcessEvent;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class ProcessPrinter
 * @package Paraunit\Printer
 */
class ProcessPrinter
{
    /** @var int */
    protected $counter = 0;

    /**
     * @param ProcessEvent $processEvent
     */
    public function onProcessTerminated(ProcessEvent $processEvent)
    {
        $process = $processEvent->getProcess();

        if ( ! $processEvent->has('output_interface')) {
            throw new \BadMethodCallException('missing output_interface');
        }

        $output = $processEvent->get('output_interface');

        if ( ! $output instanceof OutputInterface) {
            throw new \BadMethodCallException('output_interface, unexpected type: ' . get_class($output));
        }

        if ($process->isToBeRetried()) {
            $this->printWithCounter($output, '<ok>A</ok>');

            return;
        }

        if (0 == count($process->getTestResults())) {
            // TODO --- this operation should be done somewhere else!
            $process->setTestResults(array('X'));
        }

        foreach ($process->getTestResults() as $testResult) {
            $this->printSingleTestResult($output, $testResult);
        }
    }

    /**
     * @param OutputInterface $output
     * @param int $testResult
     */
    protected function printSingleTestResult(OutputInterface $output, $testResult)
    {
        switch ($testResult) {
            case 'E': 
                $this->printWithCounter($output, '<error>E</error>');
                break;
            case 'F':
                $this->printWithCounter($output, '<fail>F</fail>');
                break;
            case 'I':
                $this->printWithCounter($output, '<incomplete>I</incomplete>');
                break;
            case 'S':
                $this->printWithCounter($output, '<skipped>S</skipped>');
                break;
            case '.':
                $this->printWithCounter($output, '<ok>.</ok>');
                break;
            default:
                $this->printWithCounter($output, '<error>X</error>');
                break;
        }
    }

    /**
     * @param OutputInterface $output
     * @param string $string
     */
    protected function printWithCounter(OutputInterface $output, $string)
    {
        if ($this->counter % 80 == 0 && $this->counter > 1) {
            $output->writeln('');
        }

        ++$this->counter;

        $output->write($string);
    }
}
