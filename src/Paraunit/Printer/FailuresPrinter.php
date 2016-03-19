<?php

namespace Paraunit\Printer;

use Paraunit\Lifecycle\EngineEvent;
use Paraunit\TestResult\Interfaces\FailureMessageInterface;
use Paraunit\TestResult\Interfaces\FunctionNameInterface;
use Paraunit\TestResult\Interfaces\StackTraceInterface;
use Paraunit\TestResult\TestResultContainer;

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
        $this->output = $engineEvent->getOutputInterface();
        /** @var \DateInterval $elapsedTime */

        foreach ($this->logParser->getParsersForPrinting() as $parser) {
            if ($parser instanceof TestResultContainer) {
                $this->printFailuresOutput($parser);
            }
        }
    }

    /**
     * @param TestResultContainer $testResultContainer
     */
    private function printFailuresOutput(TestResultContainer $testResultContainer)
    {
        if ( ! $testResultContainer->getTestResultFormat()->shouldPrintTestOutput()) {
            return;
        }

        $tag = $testResultContainer->getTestResultFormat()->getTag();
        $title = $testResultContainer->getTestResultFormat()->getTitle();
        $i = 1;

        foreach ($testResultContainer->getTestResults() as $testResult) {
            if ($i == 1) {
                $this->output->writeln('');
                $this->output->writeln(sprintf('<%s>%s output:</%s>', $tag, ucwords($title), $tag));
            }

            $this->output->writeln('');
            $this->output->write(sprintf('<%s>%d) ', $tag, $i++));

            if ($testResult instanceof FunctionNameInterface) {
                $this->output->writeln($testResult->getFunctionName());
            }

            $this->output->write(sprintf('</%s>', $tag));

            if ($testResult instanceof FailureMessageInterface) {
                $this->output->writeln($testResult->getFailureMessage());
            }

            if ($testResult instanceof StackTraceInterface) {
                foreach ($testResult->getTrace() as $traceStep) {
                    $this->output->writeln((string)$traceStep);
                }
            }
        }
    }
}
