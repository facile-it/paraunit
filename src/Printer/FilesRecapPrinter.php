<?php

declare(strict_types=1);

namespace Paraunit\Printer;

use Paraunit\Configuration\ChunkSize;
use Paraunit\Lifecycle\EngineEnd;
use Paraunit\Printer\ValueObject\OutputStyle;
use Paraunit\TestResult\TestResultContainer;
use Paraunit\TestResult\ValueObject\TestIssue;
use Paraunit\TestResult\ValueObject\TestOutcome;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class FilesRecapPrinter implements EventSubscriberInterface
{
    /** @deprecated Use PrinterConfiguration::PRINT_ORDER instead */
    final public const PRINT_ORDER = PrinterConfiguration::PRINT_ORDER;

    public function __construct(
        private readonly OutputInterface $output,
        private readonly TestResultContainer $testResultContainer,
        private readonly ChunkSize $chunkSize,
    ) {}

    /**
     * @return array<class-string, string|array{0: string, 1: int}>
     */
    public static function getSubscribedEvents(): array
    {
        return [
            EngineEnd::class => ['onEngineEnd', 100],
        ];
    }

    public function onEngineEnd(): void
    {
        foreach (PrinterConfiguration::PRINT_ORDER as $status) {
            $style = OutputStyle::fromStatus($status);
            $this->printFileRecap($status, $style);
        }
    }

    private function printFileRecap(TestOutcome|TestIssue $status, OutputStyle $style): void
    {
        $filenames = $this->testResultContainer->getFileNames($status);
        $ignored = $this->testResultContainer->getIgnoredByBaseline($status);

        if ($filenames === [] && $ignored === 0) {
            return;
        }

        $fileTitle = $this->chunkSize->isChunked() ? 'chunks' : 'files';

        $this->output->writeln('');
        $this->output->writeln(
            sprintf(
                "<%s>%d {$fileTitle} with %s:</%s>",
                $style->value,
                count($filenames),
                strtoupper($status->getTitle()),
                $style->value
            )
        );

        foreach ($filenames as $fileName) {
            $this->output->writeln(sprintf(' <%s>%s</%s>', $style->value, $fileName, $style->value));
        }

        if ($ignored > 0) {
            $this->output->writeln(sprintf(
                ' <%s>(%d %s ignored by baseline)</%s>',
                $style->value,
                $ignored,
                $status->getTitle(),
                $style->value,
            ));
        }
    }
}
