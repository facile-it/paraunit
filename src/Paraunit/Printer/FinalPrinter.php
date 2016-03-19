<?php

namespace Paraunit\Printer;

use Paraunit\Lifecycle\EngineEvent;
use Paraunit\Process\AbstractParaunitProcess;
use Paraunit\TestResult\TestResultContainer;

/**
 * Class FinalPrinter.
 */
class FinalPrinter extends AbstractFinalPrinter
{
    /**
     * @param EngineEvent $engineEvent
     */
    public function onEngineEnd(EngineEvent $engineEvent)
    {
        $this->output = $engineEvent->getOutputInterface();
        /** @var \DateInterval $elapsedTime */

        $this->printExecutionTime($engineEvent);
        $this->printTestCounters($engineEvent);

        $this->output->writeln('');
    }

    /**
     * @param EngineEvent $engineEvent
     */
    private function printExecutionTime(EngineEvent $engineEvent)
    {
        /** @var \DateInterval $elapsedTime */
        $elapsedTime = $engineEvent->get('start')->diff($engineEvent->get('end'));

        $this->output->writeln('');
        $this->output->writeln('');
        $this->output->writeln($elapsedTime->format('Execution time -- %H:%I:%S '));
    }

    /**
     * @param EngineEvent $engineEvent
     */
    private function printTestCounters(EngineEvent $engineEvent)
    {
        $completedProcesses = $engineEvent->get('process_completed');
        $testsCount = 0;
        /** @var AbstractParaunitProcess $process */
        foreach ($this->logParser->getParsersForPrinting() as $parser) {
            if ($parser instanceof TestResultContainer) {
                $testsCount += $parser->countTestResults();
            }
        }

        $this->output->writeln('');
        $this->output->writeln(sprintf('Executed: %d test classes, %d tests', count($completedProcesses), $testsCount));
    }
}
