<?php

namespace Paraunit\Printer;

use Paraunit\Lifecycle\EngineEvent;
use Paraunit\TestResult\TestResultContainer;

/**
 * Class FilesRecapPrinter
 * @package Paraunit\Printer
 */
class FilesRecapPrinter extends AbstractFinalPrinter
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
                $this->printFileRecap($parser);
            }
        }
    }

    /**
     * @param TestResultContainer $testResultContainer
     */
    private function printFileRecap(TestResultContainer $testResultContainer)
    {
        if ( ! $testResultContainer->getTestResultFormat()->shouldPrintFilesRecap()) {
            return;
        }

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
}
