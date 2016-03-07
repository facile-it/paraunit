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
    /** @var  OutputInterface */
    private $output;
    
    /** @var int */
    private $counter = 0;

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

        switch (true) {
            case $process->isToBeRetried():
                $this->printWithCounter('<ok>A</ok>');
                break;
            case $process->hasSegmentationFaults():
                $this->printWithCounter('<segfault>X</segfault>');
                break;
            case $process->hasFatalErrors():
                $this->printWithCounter('<fatal>X</fatal>');
                break;
            case count($process->getTestResults()) == 0:
                $this->printWithCounter('<warning>?</warning>');
                break;
            default:
                foreach ($process->getTestResults() as $testResult) {
                    $this->printSingleTestResult($testResult);
                }
        }
    }

    /**
     * @param string $testResult
     */
    private function printSingleTestResult($testResult)
    {
        switch ($testResult) {
            case 'E': 
                $this->printWithCounter('<error>E</error>');
                break;
            case 'F':
                $this->printWithCounter('<fail>F</fail>');
                break;
            case 'W':
                $this->printWithCounter('<warning>W</warning>');
                break;
            case 'I':
                $this->printWithCounter('<incomplete>I</incomplete>');
                break;
            case 'S':
                $this->printWithCounter('<skipped>S</skipped>');
                break;
           case 'R':
                $this->printWithCounter('<risky>R</risky>');
                break;
            case '.':
                $this->printWithCounter('<ok>.</ok>');
                break;
            default:
                $this->printWithCounter('<warning>?</warning>');
                break;
        }
    }

    /**
     * @param string $string
     */
    private function printWithCounter($string)
    {
        if ($this->counter % 80 == 0 && $this->counter > 1) {
            $this->output->writeln('');
        }

        ++$this->counter;

        $this->output->write($string);
    }
}
