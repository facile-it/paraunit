<?php

namespace Paraunit\Printer;

use Paraunit\Lifecycle\EngineEvent;
use Paraunit\Parser\OutputContainerBearerInterface;
use Paraunit\Parser\ProcessOutputParser;
use Paraunit\Process\ProcessResultInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class FinalPrinter.
 */
class FinalPrinter
{
    /** @var  ProcessOutputParser */
    private $processOutputParser;
    
    /** @var  OutputInterface */
    private $output;

    /**
     * FinalPrinter constructor.
     * @param ProcessOutputParser $processOutputParser
     */
    public function __construct(ProcessOutputParser $processOutputParser)
    {
        $this->processOutputParser = $processOutputParser;
    }

    /**
     * @param EngineEvent $engineEvent
     */
    public function onEngineEnd(EngineEvent $engineEvent)
    {
        if (!$engineEvent->has('start') || !$engineEvent->has('end') || !$engineEvent->has('process_completed')) {
            throw new \BadMethodCallException('missing argument/s');
        }

        $this->output = $engineEvent->getOutputInterface();
        $elapsedTime = $engineEvent->get('start')->diff($engineEvent->get('end'));
        $completedProcesses =  $engineEvent->get('process_completed');

        $this->output->writeln('');
        $this->output->writeln('');
        $this->output->writeln($elapsedTime->format('Execution time -- %H:%I:%S '));

        $this->output->writeln('');
        $this->output->write('Executed: ');
        $this->output->write(count($completedProcesses).' test classes, ');

        $testsCount = 0;
        foreach ($completedProcesses as $process) {
            $testsCount += count($process->getTestResults());
        }

        $this->output->writeln($testsCount.' tests');

        $this->printAllFailuresOutput();
        $this->printAllFilesRecap();

        $this->output->writeln('');
    }

    private function printAllFailuresOutput()
    {
        foreach ($this->processOutputParser->getParsers() as $parser) {
            if ($parser instanceof OutputContainerBearerInterface) {
                $this->printFailuresOutput($parser->getOutputContainer());
            }
        }
    }

    /**
     * @param OutputContainer $outputContainer
     */
    private function printFailuresOutput(OutputContainer $outputContainer)
    {
        $buffer = $outputContainer->getOutputBuffer();
        $tag = $outputContainer->getTag();
        if (count($buffer)) {
            $this->output->writeln('');
            $this->output->writeln(sprintf('<%s>%s output:</%s>', $tag, $outputContainer->getTitle(), $tag));

            $i = 1;

            foreach ($buffer as $line) {
                $this->output->writeln('');
                $this->output->writeln(
                    sprintf('<%s>%d)</%s> %s', $tag, $i++, $tag, $line)
                );
            }
        }
    }

    private function printAllFilesRecap()
    {
        foreach ($this->processOutputParser->getParsers() as $parser) {
            if ($parser instanceof OutputContainerBearerInterface) {
                $this->printFileRecap($parser->getOutputContainer());
            }
        }
    }

    /**
     * @param OutputContainer $outputContainer
     */
    private function printFileRecap(OutputContainer $outputContainer)
    {
        if ($outputContainer->count()) {
            $tag = $outputContainer->getTag();
            $this->output->writeln('');
            $this->output->writeln(
                sprintf(
                    '<%s>%d files with %s:</%s>',
                    $tag,
                    $outputContainer->count(),
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
