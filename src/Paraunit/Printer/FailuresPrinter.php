<?php

declare(strict_types=1);

namespace Paraunit\Printer;

use Paraunit\Lifecycle\EngineEvent;
use Paraunit\TestResult\Interfaces\FailureMessageInterface;
use Paraunit\TestResult\Interfaces\FunctionNameInterface;
use Paraunit\TestResult\Interfaces\StackTraceInterface;
use Paraunit\TestResult\TestResultContainer;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Class FailuresPrinter
 * @package Paraunit\Printer
 */
class FailuresPrinter extends AbstractFinalPrinter implements EventSubscriberInterface
{
    public static function getSubscribedEvents()
    {
        return [
            EngineEvent::END => ['onEngineEnd', 200],
        ];
    }

    public function onEngineEnd()
    {
        foreach ($this->testResultList->getTestResultContainers() as $parser) {
            if ($parser->getTestResultFormat()->shouldPrintTestOutput()) {
                $this->printFailuresOutput($parser);
            }
        }
    }

    /**
     * @param TestResultContainer $testResultContainer
     */
    private function printFailuresOutput(TestResultContainer $testResultContainer)
    {
        if (empty($testResultContainer->getTestResults())) {
            return;
        }

        $tag = $testResultContainer->getTestResultFormat()->getTag();
        $title = $testResultContainer->getTestResultFormat()->getTitle();

        $this->getOutput()->writeln('');
        $this->getOutput()->writeln(sprintf('<%s>%s output:</%s>', $tag, ucwords($title), $tag));

        $i = 1;
        foreach ($testResultContainer->getTestResults() as $testResult) {
            $this->getOutput()->writeln('');
            $this->getOutput()->write(sprintf('<%s>%d) ', $tag, $i++));

            if ($testResult instanceof FunctionNameInterface) {
                $this->getOutput()->writeln($testResult->getFunctionName());
            }

            $this->getOutput()->write(sprintf('</%s>', $tag));

            if ($testResult instanceof FailureMessageInterface) {
                $this->getOutput()->writeln($testResult->getFailureMessage());
            }

            if ($testResult instanceof StackTraceInterface) {
                foreach ($testResult->getTrace() as $traceStep) {
                    $this->getOutput()->writeln((string) $traceStep);
                }
            }
        }
    }
}
