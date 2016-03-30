<?php

namespace Paraunit\Printer;

use Paraunit\Lifecycle\EngineEvent;
use Paraunit\TestResult\Interfaces\FailureMessageInterface;
use Paraunit\TestResult\Interfaces\FunctionNameInterface;
use Paraunit\TestResult\Interfaces\StackTraceInterface;
use Paraunit\TestResult\TestResultContainer;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class FailuresPrinter
 * @package Paraunit\Printer
 */
class FailuresPrinter extends AbstractFinalPrinter
{
    /**
     * @param EngineEvent $engineEvent
     */
    public function onEngineEnd(EngineEvent $engineEvent)
    {
        $output = $engineEvent->getOutputInterface();

        foreach ($this->logParser->getParsersForPrinting() as $parser) {
            if ($parser instanceof TestResultContainer) {
                $this->printFailuresOutput($parser, $output);
            }
        }
    }

    /**
     * @param TestResultContainer $testResultContainer
     * @param OutputInterface $output
     */
    private function printFailuresOutput(TestResultContainer $testResultContainer, OutputInterface $output)
    {
        if (! $testResultContainer->getTestResultFormat()->shouldPrintTestOutput()) {
            return;
        }

        $tag = $testResultContainer->getTestResultFormat()->getTag();
        $title = $testResultContainer->getTestResultFormat()->getTitle();
        $i = 1;

        foreach ($testResultContainer->getTestResults() as $testResult) {
            if ($i == 1) {
                $output->writeln('');
                $output->writeln(sprintf('<%s>%s output:</%s>', $tag, ucwords($title), $tag));
            }

            $output->writeln('');
            $output->write(sprintf('<%s>%d) ', $tag, $i++));

            if ($testResult instanceof FunctionNameInterface) {
                $output->writeln($testResult->getFunctionName());
            }

            $output->write(sprintf('</%s>', $tag));

            if ($testResult instanceof FailureMessageInterface) {
                $output->writeln($testResult->getFailureMessage());
            }

            if ($testResult instanceof StackTraceInterface) {
                foreach ($testResult->getTrace() as $traceStep) {
                    $output->writeln((string)$traceStep);
                }
            }
        }
    }
}
