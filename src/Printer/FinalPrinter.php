<?php

declare(strict_types=1);

namespace Paraunit\Printer;

use Paraunit\Configuration\ChunkSize;
use Paraunit\Lifecycle\AbstractEvent;
use Paraunit\Lifecycle\EngineEnd;
use Paraunit\Lifecycle\EngineStart;
use Paraunit\Lifecycle\ProcessTerminated;
use Paraunit\Lifecycle\ProcessToBeRetried;
use Paraunit\TestResult\TestResultList;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Stopwatch\Stopwatch;
use Symfony\Component\Stopwatch\StopwatchEvent;

class FinalPrinter extends AbstractFinalPrinter implements EventSubscriberInterface
{
    private const STOPWATCH_NAME = 'engine';

    private readonly Stopwatch $stopWatch;

    private int $processCompleted = 0;

    private int $processRetried = 0;
    private int $testsCount = 0;

    public function __construct(
        TestResultList $testResultList,
        OutputInterface $output,
        ChunkSize $chunkSize
    ) {
        parent::__construct($testResultList, $output, $chunkSize);

        $this->stopWatch = new Stopwatch();
    }

    /**
     * @return array<class-string<AbstractEvent>, string|array{0: string, 1: int}>
     */
    public static function getSubscribedEvents(): array
    {
        return [
            EngineStart::class => 'onEngineStart',
            EngineEnd::class => ['onEngineEnd', 300],
            ProcessTerminated::class => 'onProcessTerminated',
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

    public function onProcessTerminated(ProcessTerminated $event): void
    {
        ++$this->processCompleted;
        $this->testsCount += count($event->getProcess()->getTestResults());
    }

    public function onProcessToBeRetried(): void
    {
        ++$this->processRetried;
    }

    private function printExecutionTime(StopwatchEvent $stopEvent): void
    {
        $this->getOutput()->writeln('');
        $this->getOutput()->writeln('');
        $this->getOutput()->writeln('Execution time -- ' . gmdate('H:i:s', (int) ($stopEvent->getDuration() / 1000)));
    }

    private function printTestCounters(): void
    {
        $this->getOutput()->writeln('');
        $executedNum = $this->processCompleted - $this->processRetried;
        $executedTitle = $this->chunkSize->isChunked() ? 'chunks' : 'test classes';
        $this->getOutput()->write(sprintf("Executed: %d $executedTitle", $executedNum));
        if ($this->processRetried > 0) {
            $this->getOutput()->write(sprintf(' (%d retried)', $this->processRetried));
        }
        $this->getOutput()->write(sprintf(', %d tests', $this->testsCount));
        $this->getOutput()->writeln('');
    }
}
