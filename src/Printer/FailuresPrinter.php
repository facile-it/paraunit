<?php

declare(strict_types=1);

namespace Paraunit\Printer;

use Paraunit\Lifecycle\EngineEnd;
use Paraunit\Printer\ValueObject\OutputStyle;
use Paraunit\TestResult\TestResultContainer;
use Paraunit\TestResult\TestResultWithMessage;
use Paraunit\TestResult\ValueObject\TestIssue;
use Paraunit\TestResult\ValueObject\TestOutcome;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class FailuresPrinter implements EventSubscriberInterface
{
    public function __construct(
        private readonly OutputInterface $output,
        private readonly TestResultContainer $testResultContainer
    ) {}

    /**
     * @return array<class-string, string|array{0: string, 1: int}>
     */
    public static function getSubscribedEvents(): array
    {
        return [
            EngineEnd::class => ['onEngineEnd', 200],
        ];
    }

    public function onEngineEnd(): void
    {
        foreach (FilesRecapPrinter::PRINT_ORDER as $outcome) {
            $testResults = $this->testResultContainer->getTestResults($outcome);

            if ($testResults === []) {
                continue;
            }

            $style = OutputStyle::fromStatus($outcome);
            $counter = 1;

            $this->printFailuresHeading($outcome, $style);

            if ($outcome === TestIssue::Deprecation) {
                $this->printDeduplicated($style, ...$testResults);

                continue;
            }

            foreach ($testResults as $testResult) {
                $this->printFailureOutput($testResult, $style, $counter++);
            }
        }
    }

    private function printFailuresHeading(TestOutcome|TestIssue $outcome, OutputStyle $style): void
    {
        $this->output->writeln('');
        $this->output->writeln(sprintf('<%s>%s output:</%s>', $style->value, ucwords($outcome->getTitle()), $style->value));
    }

    private function printDeduplicated(OutputStyle $style, TestResultWithMessage ...$results): void
    {
        $deduplicated = [];
        foreach ($results as $result) {
            $deduplicated[$result->message][$result->test->name] ??= 0;
            $deduplicated[$result->message][$result->test->name] += 1;
        }

        foreach ($deduplicated as $message => $tests) {
            $this->output->writeln(sprintf('<%s>%s</%s>', $style->value, $message, $style->value));

            foreach ($tests as $testName => $count) {
                $this->output->writeln(sprintf('  %dx %s', $count, $testName));
            }
        }
    }

    private function printFailureOutput(TestResultWithMessage $testResult, OutputStyle $style, int $counter): void
    {
        $this->output->writeln('');
        $this->output->write(sprintf('<%s>%d) ', $style->value, $counter));
        $this->output->writeln($testResult->test->name);

        $this->output->write(sprintf('</%s>', $style->value));

        $this->output->writeln($testResult->message);
    }
}
