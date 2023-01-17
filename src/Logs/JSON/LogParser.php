<?php

declare(strict_types=1);

namespace Paraunit\Logs\JSON;

use Paraunit\Lifecycle\ProcessParsingCompleted;
use Paraunit\Lifecycle\ProcessTerminated;
use Paraunit\Lifecycle\ProcessToBeRetried;
use Paraunit\Logs\ValueObject\TestStatus;
use Paraunit\Printer\ValueObject\TestOutcome;
use Paraunit\TestResult\TestResult;
use Psr\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class LogParser implements EventSubscriberInterface
{
    public function __construct(
        private readonly LogFetcher $logLocator,
        private readonly LogHandler $logHandler,
        private readonly RetryParser $retryParser,
        private readonly EventDispatcherInterface $eventDispatcher,
    ) {
    }

    /**
     * @return array<string, string>
     */
    public static function getSubscribedEvents(): array
    {
        return [
            ProcessTerminated::class => 'onProcessTerminated',
        ];
    }

    public function onProcessTerminated(ProcessTerminated $processEvent): void
    {
        $process = $processEvent->getProcess();
        $logs = $this->logLocator->fetch($process);

        $testPrepared = false;

        foreach ($logs as $singleLog) {
            $testPrepared |= $singleLog->status === TestStatus::Prepared;

            if ($this->retryParser->processWillBeRetried($process, $singleLog)) {
                $this->eventDispatcher->dispatch(new ProcessToBeRetried($process));

                return;
            }

            $this->logHandler->processLog($process, $singleLog);
        }

        if ($process->getExitCode() === 0 && ! $testPrepared) {
            $process->addTestResult(new TestResult($logs[0]->test, TestOutcome::NoTestExecuted));

            return;
        }

        $this->eventDispatcher->dispatch(new ProcessParsingCompleted($process));
    }
}
