<?php

declare(strict_types=1);

namespace Paraunit\Parser\JSON;

use Paraunit\Lifecycle\ProcessParsingCompleted;
use Paraunit\Lifecycle\ProcessTerminated;
use Paraunit\Lifecycle\ProcessToBeRetried;
use Paraunit\Parser\ValueObject\LogData;
use Paraunit\Parser\ValueObject\TestStatus;
use Paraunit\Process\AbstractParaunitProcess;
use Paraunit\TestResult\Interfaces\TestResultHandlerInterface;
use Paraunit\TestResult\Interfaces\TestResultInterface;
use Psr\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class LogParser implements EventSubscriberInterface
{
    public function __construct(
        private readonly LogFetcher $logLocator,
        private readonly LogHandler $logHandler,
        private readonly TestResultHandlerInterface $noTestExecutedResultContainer,
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
            $this->noTestExecutedResultContainer->addProcessToFilenames($process);

            return;
        }

        $this->eventDispatcher->dispatch(new ProcessParsingCompleted($process));
    }
}
