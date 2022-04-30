<?php

declare(strict_types=1);

namespace Paraunit\Printer;

use Paraunit\Configuration\ChunkSize;
use Paraunit\Lifecycle\EngineEnd;
use Paraunit\Lifecycle\EngineStart;
use Paraunit\Lifecycle\ProcessTerminated;
use Paraunit\Lifecycle\ProcessToBeRetried;
use Paraunit\TestResult\Interfaces\TestResultContainerInterface;
use Paraunit\TestResult\TestResultList;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Stopwatch\Stopwatch;
use Symfony\Component\Stopwatch\StopwatchEvent;

class FinalPrinter extends AbstractFinalPrinter implements EventSubscriberInterface
{
    private const STOPWATCH_NAME = 'engine';

    /** @var Stopwatch */
    private $stopWatch;

    /** @var int */
    private $processCompleted;

    /** @var int */
    private $processRetried;

    public function __construct(
        TestResultList $testResultList,
        OutputInterface $output,
        ChunkSize $chunkSize
    ) {
        parent::__construct($testResultList, $output, $chunkSize);

        $this->stopWatch = new Stopwatch();
        $this->processCompleted = 0;
        $this->processRetried = 0;
    }

    /**
     * @return array<string, string|(string|int)[]>
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

    public function onProcessTerminated(): void
    {
        ++$this->processCompleted;
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
        $testsCount = 0;
        foreach ($this->testResultList->getTestResultContainers() as $container) {
            if ($container instanceof TestResultContainerInterface) {
                $testsCount += $container->countTestResults();
            }
        }

        $this->getOutput()->writeln('');
        $executedNum = $this->processCompleted - $this->processRetried;
        if ($this->chunkSize->isChunked()) {
            $executedTitle = 'chunks';
        } else {
            $executedTitle = 'test classes';
        }
        $this->getOutput()->write(sprintf("Executed: %d $executedTitle", $executedNum));
        if ($this->processRetried > 0) {
            $this->getOutput()->write(sprintf(' (%d retried)', $this->processRetried));
        }
        $this->getOutput()->write(sprintf(', %d tests', $testsCount - $this->processRetried));
        $this->getOutput()->writeln('');
    }
}
