<?php

declare(strict_types=1);

namespace Paraunit\Parser;

use Paraunit\Lifecycle\ProcessParsingCompleted;
use Paraunit\Parser\ValueObject\Test;
use Paraunit\Process\AbstractParaunitProcess;
use Paraunit\TestResult\Interfaces\TestResultHandlerInterface;
use Paraunit\TestResult\TestResultWithMessage;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class DeprecationParser implements EventSubscriberInterface
{
    public function __construct(private readonly TestResultHandlerInterface $testResultContainer)
    {}

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
            $testResult = $this->createTestResult($process);
            $this->testResultContainer->handleTestResult($process, $testResult);
        }
    }

    private function createTestResult(AbstractParaunitProcess $process): TestResultWithMessage
    {
        return new TestResultWithMessage(
            new Test($process->getTestClassName() ?? $process->getFilename()),
            $process->getOutput()
        );
    }
}
