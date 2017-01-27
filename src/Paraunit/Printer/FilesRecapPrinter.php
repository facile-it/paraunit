<?php
declare(strict_types=1);

namespace Paraunit\Printer;

use Paraunit\TestResult\Interfaces\TestResultContainerInterface;

/**
 * Class FilesRecapPrinter
 * @package Paraunit\Printer
 */
class FilesRecapPrinter extends AbstractFinalPrinter
{
    public function onEngineEnd()
    {
        foreach ($this->testResultList->getTestResultContainers() as $parser) {
            $this->printFileRecap($parser);
        }
    }

    /**
     * @param TestResultContainerInterface $testResultContainer
     */
    private function printFileRecap(TestResultContainerInterface $testResultContainer)
    {
        if (! $testResultContainer->getTestResultFormat()->shouldPrintFilesRecap()) {
            return;
        }

        $filenames = $testResultContainer->getFileNames();

        if (count($filenames)) {
            $tag = $testResultContainer->getTestResultFormat()->getTag();
            $title = $testResultContainer->getTestResultFormat()->getTitle();
            $this->getOutput()->writeln('');
            $this->getOutput()->writeln(
                sprintf(
                    '<%s>%d files with %s:</%s>',
                    $tag,
                    count($filenames),
                    strtoupper($title),
                    $tag
                )
            );

            foreach ($filenames as $fileName) {
                $this->getOutput()->writeln(sprintf(' <%s>%s</%s>', $tag, $fileName, $tag));
            }
        }
    }
}
