<?php

declare(strict_types=1);

namespace Paraunit\Parser;

use Paraunit\Lifecycle\ProcessParsingCompleted;
use Paraunit\Process\AbstractParaunitProcess;
use Paraunit\TestResult\Interfaces\TestResultHandlerInterface;
use Paraunit\TestResult\TestResultWithMessage;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class DeprecationParser implements EventSubscriberInterface
{
    /** @var TestResultHandlerInterface */
    private $testResultContainer;

    public function __construct(TestResultHandlerInterface $testResultContainer)
    {
        $this->testResultContainer = $testResultContainer;
    }

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

        if (strpos($process->getOutput(), 'deprecation') !== false) {
            $testResult = $this->createTestResult($process);
            $this->testResultContainer->handleTestResult($process, $testResult);
        }
    }

    private function createTestResult(AbstractParaunitProcess $process): TestResultWithMessage
    {
        return new TestResultWithMessage(
            $process->getTestClassName() ?? $process->getFilename(),
            $process->getOutput()
        );
    }
}
