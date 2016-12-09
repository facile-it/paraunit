<?php

namespace Paraunit\Printer;

use Paraunit\Lifecycle\EngineEvent;

/**
 * Class FinalPrinter
 * @package Paraunit\Printer
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
        foreach ($this->testResultList->getTestResultContainers() as $container) {
            $testsCount += $container->countTestResults();
        }

        $output->writeln('');
        $output->writeln(sprintf('Executed: %d test classes, %d tests', count($completedProcesses), $testsCount));
        $output->writeln('');
    }
}
