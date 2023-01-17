<?php

declare(strict_types=1);

namespace Paraunit\Logs;

use Paraunit\Lifecycle\ProcessParsingCompleted;
use Paraunit\Logs\ValueObject\Test;
use Paraunit\Printer\ValueObject\TestOutcome;
use Paraunit\Process\AbstractParaunitProcess;
use Paraunit\TestResult\TestResultWithMessage;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class DeprecationParser implements EventSubscriberInterface
{
    public function __construct()
    {
        // TODO - check
    }

    /**
     * @return array<string, string>
     */
    public static function getSubscribedEvents(): array
    {
        return [
            ProcessParsingCompleted::class => 'handleDeprecations',
        ];
    }

    public function handleDeprecations(ProcessParsingCompleted $event): void
    {
        $process = $event->getProcess();

        if ($process->getExitCode() === 0) {
            return;
        }

        if (str_contains($process->getOutput(), 'deprecation')) {
            $this->createTestResult($process);
            // TODO
//            $this->testResultContainer->handleTestResult($process, $testResult);
        }
    }

    private function createTestResult(AbstractParaunitProcess $process): TestResultWithMessage
    {
        return new TestResultWithMessage(
            new Test($process->getTestClassName() ?? $process->getFilename()),
            TestOutcome::Deprecation,
            $process->getOutput(),
        );
    }
}
