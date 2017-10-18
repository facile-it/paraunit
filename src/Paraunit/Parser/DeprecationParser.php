<?php

namespace Paraunit\Parser;

use Paraunit\Lifecycle\ProcessEvent;
use Paraunit\Process\AbstractParaunitProcess;
use Paraunit\TestResult\Interfaces\TestResultContainerInterface;
use Paraunit\TestResult\Interfaces\TestResultHandlerInterface;
use Paraunit\TestResult\TestResultWithMessage;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class DeprecationParser implements EventSubscriberInterface
{
    /** @var TestResultHandlerInterface */
    private $testResultContainer;

    /**
     * DeprecationParser constructor.
     * @param TestResultContainerInterface $testResultContainer
     */
    public function __construct(TestResultContainerInterface $testResultContainer)
    {
        $this->testResultContainer = $testResultContainer;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            ProcessEvent::PROCESS_PARSING_COMPLETED => 'handleDeprecations',
        ];
    }

    public function handleDeprecations(ProcessEvent $event)
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
