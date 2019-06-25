<?php

declare(strict_types=1);

namespace Paraunit\Printer;

use Paraunit\Lifecycle\EngineEnd;
use Paraunit\TestResult\Interfaces\FailureMessageInterface;
use Paraunit\TestResult\Interfaces\FunctionNameInterface;
use Paraunit\TestResult\Interfaces\StackTraceInterface;
use Paraunit\TestResult\TestResultContainer;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class FailuresPrinter extends AbstractFinalPrinter implements EventSubscriberInterface
{
    public static function getSubscribedEvents(): array
    {
        return [
            EngineEnd::class => ['onEngineEnd', 200],
        ];
    }

    public function onEngineEnd(): void
    {
        foreach ($this->testResultList->getTestResultContainers() as $parser) {
            if ($parser->getTestResultFormat()->shouldPrintTestOutput()) {
                $this->printFailuresOutput($parser);
            }
        }
    }

    private function printFailuresOutput(TestResultContainer $testResultContainer): void
    {
        if (empty($testResultContainer->getTestResults())) {
            return;
        }

        $tag = $testResultContainer->getTestResultFormat()->getTag();
        $title = $testResultContainer->getTestResultFormat()->getTitle();

        $output = $this->getOutput();
        $output->writeln('');
        $output->writeln(sprintf('<%s>%s output:</%s>', $tag, ucwords($title), $tag));

        $i = 1;
        foreach ($testResultContainer->getTestResults() as $testResult) {
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
                $output->writeln($testResult->getTrace());
            }
        }
    }
}
