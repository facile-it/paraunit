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
    /** @var ParserChainElementInterface[] */
    private array $parsers = [];

    public function __construct(private readonly LogFetcher $logLocator, private readonly TestResultHandlerInterface $noTestExecutedResultContainer, private readonly EventDispatcherInterface $eventDispatcher, private readonly RetryParser $retryParser)
    {
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

    public function addParser(ParserChainElementInterface $container): void
    {
        $this->parsers[] = $container;
    }

    public function onProcessTerminated(ProcessTerminated $processEvent): void
    {
        $process = $processEvent->getProcess();
        $logs = $this->logLocator->fetch($process);

        if ($this->noTestsExecuted($process, $logs)) {
            $this->noTestExecutedResultContainer->addProcessToFilenames($process);

            return;
        }

        if ($this->retryParser->processWillBeRetried($process, $logs)) {
            $this->eventDispatcher->dispatch(new ProcessToBeRetried($process));

            return;
        }

        foreach ($logs as $singleLog) {
            $this->processLog($process, $singleLog);
        }

        $this->eventDispatcher->dispatch(new ProcessParsingCompleted($process));
    }

    private function processLog(AbstractParaunitProcess $process, LogData $logItem): void
    {
        foreach ($this->parsers as $resultContainer) {
            if ($resultContainer->handleLogItem($process, $logItem) instanceof TestResultInterface) {
                return;
            }
        }
    }

    /**
     * @param LogData[] $logs
     */
    private function noTestsExecuted(AbstractParaunitProcess $process, array $logs): bool
    {
        if ($process->getExitCode() !== 0) {
            return false;
        }

        foreach ($logs as $log) {
            if ($log->status === TestStatus::Prepared) {
                return false;
            }
        }

        return true;
    }
}
