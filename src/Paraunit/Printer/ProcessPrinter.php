<?php

namespace Paraunit\Printer;


use Paraunit\Process\ProcessResultInterface;
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
     * @param OutputInterface $output
     * @param ProcessResultInterface $process
     */
    public function printProcessResult(OutputInterface $output, ProcessResultInterface $process)
    {
        if ($process->isToBeRetried()) {
            $this->printWithCounter($output, "<ok>A</ok>");
        } else {
            if (count($process->getTestResults())) {
                foreach ($process->getTestResults() as $testResult) {
                    $this->printSingleTestResult($output, $testResult);
                }
            } else {
                $this->printWithCounter($output, "<error>X</error>");
            }
        }
    }

    /**
     * @param OutputInterface $output
     * @param int $testResult
     */
    protected function printSingleTestResult(OutputInterface $output, $testResult)
    {
        switch ($testResult) {
            case 'E' : {
                $this->printWithCounter($output, "<error>E</error>");
                break;
            }
            case 'F' : {
                $this->printWithCounter($output, "<fail>F</fail>");
                break;
            }
            case 'I' : {
                $this->printWithCounter($output, "<incomplete>I</incomplete>");
                break;
            }
            case 'S' : {
                $this->printWithCounter($output, "<skipped>S</skipped>");
                break;
            }
            case '.' : {
                $this->printWithCounter($output, "<ok>.</ok>");
                break;
            }
            default: {
                $this->printWithCounter($output, "<error>X</error>");
                break;
            }
        }
    }

    /**
     * @param OutputInterface $output
     * @param string $string
     */
    protected function printWithCounter(OutputInterface $output, $string)
    {
        if ($this->counter % 80 == 0 && $this->counter > 1) {
            $output->writeln("");
        }

        $this->counter++;

        $output->write($string);
    }
}
