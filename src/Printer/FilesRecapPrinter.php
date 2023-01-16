<?php

declare(strict_types=1);

namespace Paraunit\Printer;

use Paraunit\Lifecycle\AbstractEvent;
use Paraunit\Lifecycle\EngineEnd;
use Paraunit\TestResult\Interfaces\TestResultContainerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class FilesRecapPrinter extends AbstractFinalPrinter implements EventSubscriberInterface
{
    /**
     * @return array<class-string<AbstractEvent>, string|array{0: string, 1: int}>
     */
    public static function getSubscribedEvents(): array
    {
        return [
            EngineEnd::class => ['onEngineEnd', 100],
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
        $fileTitle = $this->chunkSize->isChunked() ? 'chunks' : 'files';

        if (count($filenames) > 0) {
            $tag = $testResultContainer->getTestResultFormat()->getTag();
            $title = $testResultContainer->getTestResultFormat()->getTitle();
            $this->getOutput()->writeln('');
            $this->getOutput()->writeln(
                sprintf(
                    "<%s>%d $fileTitle with %s:</%s>",
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
