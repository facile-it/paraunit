<?php

declare(strict_types=1);

namespace Paraunit\Printer;

use Paraunit\Configuration\ChunkSize;
use Paraunit\Lifecycle\EngineEnd;
use Paraunit\Lifecycle\EngineStart;
use Paraunit\Lifecycle\ProcessParsingCompleted;
use Paraunit\Lifecycle\ProcessToBeRetried;
use Paraunit\Lifecycle\TestCompleted;
use Paraunit\TestResult\ValueObject\TestOutcome;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Stopwatch\Stopwatch;
use Symfony\Component\Stopwatch\StopwatchEvent;

class FinalPrinter implements EventSubscriberInterface
{
    private const STOPWATCH_NAME = 'engine';

    private readonly Stopwatch $stopWatch;

    private int $processCompleted = 0;

    private int $processRetried = 0;

    private int $testsCount = 0;

    public function __construct(
        private readonly OutputInterface $output,
        private readonly ChunkSize $chunkSize
    ) {
        $this->stopWatch = new Stopwatch();
    }

    /**
     * @return array<class-string, string|array{0: string, 1: int}>
     */
    public static function getSubscribedEvents(): array
    {
        return [
            EngineStart::class => 'onEngineStart',
            EngineEnd::class => ['onEngineEnd', 300],
            TestCompleted::class => 'onTestCompleted',
            ProcessParsingCompleted::class => 'onProcessParsingCompleted',
            ProcessToBeRetried::class => 'onProcessToBeRetried',
        ];
    }

    public function onEngineStart(): void
    {
        $this->stopWatch->start(self::STOPWATCH_NAME);
    }

    public function onEngineEnd(): void
    {
        $stopEvent = $this->stopWatch->stop(self::STOPWATCH_NAME);

        $this->printExecutionTime($stopEvent);
        $this->printTestCounters();
    }

    public function onTestCompleted(TestCompleted $event): void
    {
        if ($event->outcome !== TestOutcome::AbnormalTermination) {
            ++$this->testsCount;
        }
    }

    public function onProcessParsingCompleted(): void
    {
        ++$this->processCompleted;
    }

    public function onProcessToBeRetried(): void
    {
        ++$this->processRetried;
    }

    private function printExecutionTime(StopwatchEvent $stopEvent): void
    {
        $this->output->writeln('');
        $this->output->writeln('');
        $this->output->writeln('Execution time -- ' . gmdate('H:i:s', (int) ($stopEvent->getDuration() / 1000)));
    }

    private function printTestCounters(): void
    {
        $this->output->writeln('');
        $executedTitle = $this->chunkSize->isChunked() ? 'chunks' : 'test classes';

        $this->output->write(sprintf("Executed: %d $executedTitle", $this->processCompleted));

        if ($this->processRetried > 0) {
            $this->output->write(sprintf(' (%d retried)', $this->processRetried));
        }

        $this->output->writeln(sprintf(', %d tests', $this->testsCount));
    }
}
