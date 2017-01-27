<?php
declare(strict_types=1);

namespace Paraunit\Printer;

use Paraunit\TestResult\TestResultContainer;
use Paraunit\TestResult\TestResultList;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Stopwatch\Stopwatch;
use Symfony\Component\Stopwatch\StopwatchEvent;

/**
 * Class FinalPrinter
 * @package Paraunit\Printer
 */
class FinalPrinter extends AbstractFinalPrinter
{
    const STOPWATCH_NAME = 'engine';

    /** @var Stopwatch */
    private $stopWatch;

    /**
     * FinalPrinter constructor.
     * @param TestResultList $testResultList
     * @param OutputInterface $output
     */
    public function __construct(TestResultList $testResultList, OutputInterface $output)
    {
        parent::__construct($testResultList, $output);

        $this->stopWatch = new Stopwatch();
    }

    public function onEngineStart()
    {
        $this->stopWatch->start(self::STOPWATCH_NAME);
    }

    public function onEngineEnd()
    {
        $stopEvent = $this->stopWatch->stop(self::STOPWATCH_NAME);

        $this->printExecutionTime($stopEvent);
        $this->printTestCounters();
    }

    private function printExecutionTime(StopwatchEvent $stopEvent)
    {
        $this->getOutput()->writeln('');
        $this->getOutput()->writeln('');
        $this->getOutput()->writeln('Execution time -- ' . gmdate('H:i:s', $stopEvent->getDuration() / 1000));
    }

    private function printTestCounters()
    {
        $completedProcesses = 0; // TODO $engineEvent->get('process_completed');
        $testsCount = 0;
        foreach ($this->testResultList->getTestResultContainers() as $container) {
            if ($container instanceof TestResultContainer) {
                $testsCount += $container->countTestResults();
            }
        }

        $this->getOutput()->writeln('');
        $this->getOutput()->writeln(sprintf('Executed: %d test classes, %d tests', count($completedProcesses), $testsCount));
        $this->getOutput()->writeln('');
    }
}
