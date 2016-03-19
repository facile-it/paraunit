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
        $this->printExecutionTime($engineEvent);
        $this->printTestCounters($engineEvent);
    }

    /**
     * @param EngineEvent $engineEvent
     */
    private function printExecutionTime(EngineEvent $engineEvent)
    {
        $output = $engineEvent->getOutputInterface();
        /** @var \DateInterval $elapsedTime */
        $elapsedTime = $engineEvent->get('start')->diff($engineEvent->get('end'));

        $output->writeln('');
        $output->writeln('');
        $output->writeln($elapsedTime->format('Execution time -- %H:%I:%S '));
    }

    /**
     * @param EngineEvent $engineEvent
     */
    private function printTestCounters(EngineEvent $engineEvent)
    {
        $output = $engineEvent->getOutputInterface();
        $completedProcesses = $engineEvent->get('process_completed');
        $testsCount = 0;
        /** @var AbstractParaunitProcess $process */
        foreach ($this->logParser->getParsersForPrinting() as $parser) {
            if ($parser instanceof TestResultContainer) {
                $testsCount += $parser->countTestResults();
            }
        }

        $output->writeln('');
        $output->writeln(sprintf('Executed: %d test classes, %d tests', count($completedProcesses), $testsCount));
        $output->writeln('');
    }
}
