<?php

declare(strict_types=1);

namespace Paraunit\Printer;

use Paraunit\Lifecycle\EngineEvent;
use Paraunit\TestResult\Interfaces\TestResultContainerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class FilesRecapPrinter extends AbstractFinalPrinter implements EventSubscriberInterface
{
    public static function getSubscribedEvents(): array
    {
        return [
            EngineEvent::END => ['onEngineEnd', 100],
        ];
    }

    public function onEngineEnd(): void
    {
        foreach ($this->testResultList->getTestResultContainers() as $parser) {
            $this->printFileRecap($parser);
        }
    }

    private function printFileRecap(TestResultContainerInterface $testResultContainer): void
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
