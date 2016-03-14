<?php

namespace Paraunit\Printer;

use Paraunit\Lifecycle\EngineEvent;
use Paraunit\Output\OutputContainerInterface;
use Paraunit\Parser\JSONLogParser;
use Paraunit\Parser\OutputContainerBearerInterface;
use Paraunit\Process\ParaunitProcessAbstract;
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
        if ( ! $engineEvent->has('start') || ! $engineEvent->has('end') || ! $engineEvent->has('process_completed')) {
            throw new \BadMethodCallException('missing argument/s');
        }

        $this->output = $engineEvent->getOutputInterface();
        /** @var \DateInterval $elapsedTime */
        $elapsedTime = $engineEvent->get('start')->diff($engineEvent->get('end'));
        $completedProcesses = $engineEvent->get('process_completed');

        $this->output->writeln('');
        $this->output->writeln('');
        $this->output->writeln($elapsedTime->format('Execution time -- %H:%I:%S '));

        $testsCount = 0;
        /** @var ParaunitProcessAbstract $process */
        foreach ($completedProcesses as $process) {
            $testsCount += count($process->getTestResults());
        }

        $this->output->writeln('');
        $this->output->writeln(sprintf('Executed: %d test classes, %d tests', count($completedProcesses), $testsCount));

        $this->printAllFailuresOutput();
        $this->printAllFilesRecap();

        $this->output->writeln('');
    }

    private function printAllFailuresOutput()
    {
        $this->printFailuresOutput($this->logParser->getAbnormalTerminatedOutputContainer());

        foreach ($this->logParser->getParsersForPrinting() as $parser) {
            if ($parser instanceof OutputContainerBearerInterface) {
                $this->printFailuresOutput($parser->getOutputContainer());
            }
        }
    }

    /**
     * @param OutputContainerInterface $outputContainer
     */
    private function printFailuresOutput(OutputContainerInterface $outputContainer)
    {
        if ($outputContainer->countMessages()) {
            $buffer = $outputContainer->getOutputBuffer();
            $tag = $outputContainer->getTag();
            $this->output->writeln('');
            $this->output->writeln(sprintf('<%s>%s output:</%s>', $tag, ucwords($outputContainer->getTitle()), $tag));

            $i = 1;

            foreach ($buffer as $filename => $messages) {
                foreach ($messages as $message) {
                    $this->output->writeln('');
                    $this->output->writeln(
                        sprintf('<%s>%d)</%s> %s', $tag, $i++, $tag, $message)
                    );
                }
            }
        }
    }

    private function printAllFilesRecap()
    {
        $this->printFileRecap($this->logParser->getAbnormalTerminatedOutputContainer());

        foreach ($this->logParser->getParsersForPrinting() as $parser) {
            if ($parser instanceof OutputContainerBearerInterface) {
                $this->printFileRecap($parser->getOutputContainer());
            }
        }
    }

    /**
     * @param OutputContainerInterface $outputContainer
     */
    private function printFileRecap(OutputContainerInterface $outputContainer)
    {
        if ($outputContainer->countFiles()) {
            $tag = $outputContainer->getTag();
            $this->output->writeln('');
            $this->output->writeln(
                sprintf(
                    '<%s>%d files with %s:</%s>',
                    $tag,
                    $outputContainer->countFiles(),
                    strtoupper($outputContainer->getTitle()),
                    $tag
                )
            );

            foreach ($outputContainer->getFileNames() as $fileName) {
                $this->output->writeln(sprintf(' <%s>%s</%s>', $tag, $fileName, $tag));
            }
        }
    }
}
