<?php

namespace Paraunit\Printer;

use Paraunit\Lifecycle\EngineEvent;
use Paraunit\Output\OutputContainerInterface;
use Paraunit\Parser\JSONLogParser;
use Paraunit\Parser\OutputContainerBearerInterface;
use Paraunit\Process\AbstractParaunitProcess;
use Paraunit\TestResult\FailureMessageInterface;
use Paraunit\TestResult\FunctionNameInterface;
use Paraunit\TestResult\StackTraceInterface;
use Paraunit\TestResult\TestResultContainer;
use Paraunit\TestResult\TestResultContainerInterface;
use Paraunit\TestResult\TestResultWithMessage;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class FinalPrinter.
 */
class FinalPrinter
{
    /** @var  JSONLogParser */
    private $logParser;

    /** @var  OutputInterface */
    private $output;

    /**
     * FinalPrinter constructor.
     * @param JSONLogParser $logParser
     */
    public function __construct(JSONLogParser $logParser)
    {
        $this->logParser = $logParser;
    }

    /**
     * @param EngineEvent $engineEvent
     */
    public function onEngineEnd(EngineEvent $engineEvent)
    {
        $this->output = $engineEvent->getOutputInterface();
        /** @var \DateInterval $elapsedTime */

        $this->printExecutionTime($engineEvent);
        $this->printTestCounters($engineEvent);
        $this->printAllFailuresOutput();
        $this->printAllFilesRecap();

        $this->output->writeln('');
    }

    private function printAllFailuresOutput()
    {
        foreach ($this->logParser->getParsers() as $parser) {
            if ($parser instanceof TestResultContainer) {
                $this->printFailuresOutput($parser);
            }
        }
    }

    /**
     * @param TestResultContainer $testResultContainer
     * @todo Refactor
     */
    private function printFailuresOutput(TestResultContainer $testResultContainer)
    {
        $tag = $testResultContainer->getTestResultFormat()->getTag();
        $title = $testResultContainer->getTestResultFormat()->getTitle();
        $i = 1;

        foreach ($testResultContainer->getTestResults() as $testResult) {
            if ($testResult instanceof FunctionNameInterface) {
                if ($i == 1) {
                    $this->output->writeln('');
                    $this->output->writeln(sprintf('<%s>%s output:</%s>', $tag, ucwords($title), $tag));
                }

                $this->output->writeln('');
                $this->output->writeln(
                    sprintf('<%s>%d) %s</%s>', $tag, $i++, $testResult->getFunctionName(), $tag)
                );

                if ($testResult instanceof FailureMessageInterface) {
                    $this->output->writeln($testResult->getFailureMessage());
                }

                if ($testResult instanceof StackTraceInterface) {
                    foreach ($testResult->getTrace() as $traceStep) {
                        $this->output->writeln((string) $traceStep);
                    }
                }
            }
        }
    }

    private function printAllFilesRecap()
    {
        foreach ($this->logParser->getParsers() as $parser) {
            if ($parser instanceof TestResultContainer) {
                $this->printFileRecap($parser);
            }
        }
    }

    /**
     * @param TestResultContainer $testResultContainer
     */
    private function printFileRecap(TestResultContainer $testResultContainer)
    {
        $filenames = $testResultContainer->getFileNames();

        if (count($filenames)) {
            $tag = $testResultContainer->getTestResultFormat()->getTag();
            $title = $testResultContainer->getTestResultFormat()->getTitle();
            $this->output->writeln('');
            $this->output->writeln(
                sprintf(
                    '<%s>%d files with %s:</%s>',
                    $tag,
                    count($filenames),
                    strtoupper($title),
                    $tag
                )
            );

            foreach ($filenames as $fileName) {
                $this->output->writeln(sprintf(' <%s>%s</%s>', $tag, $fileName, $tag));
            }
        }
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
        foreach ($this->logParser->getParsers() as $parser) {
            if ($parser instanceof TestResultContainer) {
                $testsCount += $parser->countTestResults();
            }
        }

        $this->output->writeln('');
        $this->output->writeln(sprintf('Executed: %d test classes, %d tests', count($completedProcesses), $testsCount));
    }
}
