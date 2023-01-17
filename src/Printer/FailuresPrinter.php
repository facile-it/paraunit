<?php

declare(strict_types=1);

namespace Paraunit\Printer;

use Paraunit\Lifecycle\AbstractEvent;
use Paraunit\Lifecycle\EngineEnd;
use Paraunit\Printer\ValueObject\OutputStyle;
use Paraunit\Printer\ValueObject\TestOutcome;
use Paraunit\TestResult\TestResultContainer;
use Paraunit\TestResult\TestResultWithMessage;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class FailuresPrinter implements EventSubscriberInterface
{
    public function __construct(
        private readonly OutputInterface $output,
        private readonly TestResultContainer $testResultContainer
    ) {
    }

    /**
     * @return array<class-string<AbstractEvent>, string|array{0: string, 1: int}>
     */
    public static function getSubscribedEvents(): array
    {
        return [
            EngineEnd::class => ['onEngineEnd', 200],
        ];
    }

    public function onEngineEnd(): void
    {
        foreach (TestOutcome::PRINT_ORDER as $outcome) {
            if ($outcome === TestOutcome::Passed) {
                continue;
            }

            $style = OutputStyle::fromOutcome($outcome);
            $counter = 1;

            $this->printFailuresHeading($outcome, $style);

            foreach ($this->testResultContainer->getTestResults($outcome) as $testResult) {
                $this->printFailureOutput($testResult, $style, $counter++);
            }
        }
    }

    private function printFailuresHeading(TestOutcome $outcome, OutputStyle $style): void
    {
        $this->output->writeln('');
        $this->output->writeln(sprintf('<%s>%s output:</%s>', $style, ucwords($outcome->getTitle()), $style));
    }

    private function printFailureOutput(TestResultWithMessage $testResult, OutputStyle $style, int $counter): void
    {
        $this->output->writeln('');
        $this->output->write(sprintf('<%s>%d) ', $style, $counter));
        $this->output->writeln($testResult->test->name);

        $this->output->write(sprintf('</%s>', $style));

        $this->output->writeln($testResult->message);
    }
}
