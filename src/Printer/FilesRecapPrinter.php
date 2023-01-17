<?php

declare(strict_types=1);

namespace Paraunit\Printer;

use Paraunit\Configuration\ChunkSize;
use Paraunit\Lifecycle\AbstractEvent;
use Paraunit\Lifecycle\EngineEnd;
use Paraunit\Printer\ValueObject\OutputStyle;
use Paraunit\Printer\ValueObject\TestOutcome;
use Paraunit\TestResult\TestResultContainer;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class FilesRecapPrinter implements EventSubscriberInterface
{
    public function __construct(
        private readonly OutputInterface $output,
        private readonly TestResultContainer $testResultContainer,
        private readonly ChunkSize $chunkSize,
    ) {}

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
        foreach (TestOutcome::PRINT_ORDER as $outcome) {
            if ($outcome === TestOutcome::Passed) {
                continue;
            }

            $style = OutputStyle::fromOutcome($outcome);
            $this->printFileRecap($outcome, $style);
        }
    }

    private function printFileRecap(TestOutcome $outcome, OutputStyle $style): void
    {
        $filenames = $this->testResultContainer->getFileNames($outcome);

        if ($filenames === []) {
            return;
        }

        $fileTitle = $this->chunkSize->isChunked() ? 'chunks' : 'files';

        $this->output->writeln('');
        $this->output->writeln(
            sprintf(
                "<%s>%d $fileTitle with %s:</%s>",
                $style->value,
                count($filenames),
                strtoupper($outcome->getTitle()),
                $style->value
            )
        );

        foreach ($filenames as $fileName) {
            $this->output->writeln(sprintf(' <%s>%s</%s>', $style->value, $fileName, $style->value));
        }
    }
}
