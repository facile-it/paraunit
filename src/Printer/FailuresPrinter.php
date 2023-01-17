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

class FailuresPrinter extends AbstractPrinter implements EventSubscriberInterface
{
    public function __construct(OutputInterface $output, private readonly TestResultContainer $testResultContainer)
    {
        parent::__construct($output);
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
        $output = $this->getOutput();

        $output->writeln('');
        $output->writeln(sprintf('<%s>%s output:</%s>', $style, ucwords($outcome->getTitle()), $style));
    }

    private function printFailureOutput(TestResultWithMessage $testResult, OutputStyle $style, int $counter): void
    {
        $output = $this->getOutput();

        $output->writeln('');
        $output->write(sprintf('<%s>%d) ', $style, $counter));
        $output->writeln($testResult->test->name);

        $output->write(sprintf('</%s>', $style));

        $output->writeln($testResult->message);
    }
}
