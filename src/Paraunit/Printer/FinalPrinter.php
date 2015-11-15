<?php

namespace Paraunit\Printer;

use Paraunit\Lifecycle\EngineEvent;
use Paraunit\Process\ProcessResultInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class FinalPrinter.
 */
class FinalPrinter
{
    /**
     * @var OutputContainer[]
     */
    protected $outputContainers;

    /**
     * @var OutputContainer
     */
    protected $fatalErrors;

    /**
     * @var OutputContainer
     */
    protected $errors;

    /**
     * @var OutputContainer
     */
    protected $failures;

    /**
     * @var OutputContainer
     */
    protected $skipped;

    /**
     * @var OutputContainer
     */
    protected $incomplete;

    /**
     * @var OutputContainer
     */
    protected $unknownStatus;

    public function __construct()
    {
        $this->segmentationFaults = new OutputContainer('error', 'SEGMENTATION FAULTS');
        $this->unknownStatus = new OutputContainer('error', 'UNKNOWN STATUS');
        $this->fatalErrors = new OutputContainer('error', 'FATAL ERRORS');
        $this->errors = new OutputContainer('error', 'ERRORS');
        $this->failures = new OutputContainer('fail', 'FAILURES');
        $this->skipped = new OutputContainer('skipped', 'SKIPPED');
        $this->incomplete = new OutputContainer('incomplete', 'INCOMPLETE');

        $this->outputContainers = array(
            $this->segmentationFaults,
            $this->unknownStatus,
            $this->fatalErrors,
            $this->errors,
            $this->failures,
            $this->skipped,
            $this->incomplete,
        );
    }

    /**
     * @param EngineEvent $engineEvent
     */
    public function onEngineEnd(EngineEvent $engineEvent)
    {

        if (!$engineEvent->has('start') || !$engineEvent->has('end') || !$engineEvent->has('process_completed')){
            throw new \BadMethodCallException('missing argument/s');
        }

        $outputInterface =  $engineEvent->getOutputInterface();
        $elapsedTime =  $engineEvent->get('start')->diff($engineEvent->get('end'));
        $completedProcesses =  $engineEvent->get('process_completed');

        $outputInterface->writeln('');
        $outputInterface->writeln('');
        $outputInterface->writeln($elapsedTime->format('Execution time -- %H:%I:%S '));

        $outputInterface->writeln('');
        $outputInterface->write('Executed: ');
        $outputInterface->write(count($completedProcesses).' test classes, ');

        $testsCount = 0;
        foreach ($completedProcesses as $process) {
            $this->analyzeProcess($process);
            $testsCount += count($process->getTestResults());
        }

        $outputInterface->writeln($testsCount.' tests');

        foreach ($this->outputContainers as $outputContainer) {
            $this->printFailuresOutput($outputInterface, $outputContainer);
        }

        foreach ($this->outputContainers as $outputContainer) {
            $this->printFileRecap($outputInterface, $outputContainer);
        }

        $outputInterface->writeln('');
    }

    /**
     * @param ProcessResultInterface $process
     */
    protected function analyzeProcess(ProcessResultInterface $process)
    {
        $filename = $process->getFilename();

        if ($process->hasSegmentationFaults()) {
            $this->segmentationFaults->addFileName($filename);
        }

        if ($process->hasFatalErrors()) {
            $this->fatalErrors->addFileName($filename);
            $this->fatalErrors->addToOutputBuffer($process->getFatalErrors());
        } else if (in_array('X', $process->getTestResults())) {
            $this->unknownStatus->addFileName($filename);
            $this->unknownStatus->addToOutputBuffer($process->getOutput());
        }

        if ($process->hasErrors()) {
            $this->errors->addFileName($filename);
            $this->errors->addToOutputBuffer($process->getErrors());
        }

        if ($process->hasFailures()) {
            $this->failures->addFileName($filename);
            $this->failures->addToOutputBuffer($process->getFailures());
        }

        if (in_array('S', $process->getTestResults())) {
            $this->skipped->addFileName($filename);
        }

        if (in_array('I', $process->getTestResults())) {
            $this->incomplete->addFileName($filename);
        }
    }

    /**
     * @param OutputInterface $outputInterface
     * @param OutputContainer $outputContainer
     */
    protected function printFileRecap(OutputInterface $outputInterface, OutputContainer $outputContainer)
    {
        $tag = $outputContainer->getTag();
        if ($outputContainer->count()) {
            $outputInterface->writeln('');
            $outputInterface->writeln(
                sprintf(
                    '<%s>%d files with %s:</%s>',
                    $tag,
                    $outputContainer->count(),
                    $outputContainer->getTitle(),
                    $tag
                )
            );

            foreach ($outputContainer->getFileNames() as $fileName) {
                $outputInterface->writeln(sprintf(' <%s>%s</%s>', $tag, $fileName, $tag));
            }
        }
    }

    /**
     * @param OutputInterface $outputInterface
     * @param OutputContainer $outputContainer
     */
    protected function printFailuresOutput(OutputInterface $outputInterface, OutputContainer $outputContainer)
    {
        $buffer = $outputContainer->getOutputBuffer();
        $tag = $outputContainer->getTag();
        if (count($buffer)) {
            $outputInterface->writeln('');
            $outputInterface->writeln(sprintf('<%s>%s output:</%s>', $tag, $outputContainer->getTitle(), $tag));

            $i = 1;

            foreach ($buffer as $line) {
                $outputInterface->writeln('');
                $outputInterface->writeln(
                    sprintf('<%s>%d)</%s> %s', $tag, $i++, $tag, $line)
                );
            }
        }
    }
}
