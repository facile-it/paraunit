<?php

declare(strict_types=1);

namespace Paraunit\Parser\JSON;

use Paraunit\Lifecycle\ProcessParsingCompleted;
use Paraunit\Lifecycle\ProcessTerminated;
use Paraunit\Lifecycle\ProcessToBeRetried;
use Paraunit\Process\AbstractParaunitProcess;
use Paraunit\TestResult\Interfaces\TestResultHandlerInterface;
use Paraunit\TestResult\Interfaces\TestResultInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class LogParser implements EventSubscriberInterface
{
    /** @var LogFetcher */
    private $logLocator;

    /** @var TestResultHandlerInterface */
    private $noTestExecutedResultContainer;

    /** @var EventDispatcherInterface */
    private $eventDispatcher;

    /** @var RetryParser */
    private $retryParser;

    /** @var ParserChainElementInterface[] */
    private $parsers;

    public function __construct(
        LogFetcher $logLocator,
        TestResultHandlerInterface $noTestExecutedResultContainer,
        EventDispatcherInterface $eventDispatcher,
        RetryParser $retryParser
    ) {
        $this->logLocator = $logLocator;
        $this->noTestExecutedResultContainer = $noTestExecutedResultContainer;
        $this->eventDispatcher = $eventDispatcher;
        $this->retryParser = $retryParser;
        $this->parsers = [];
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

    /**
     * @return ParserChainElementInterface[]
     */
    public function getParsers(): array
    {
        return $this->parsers;
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

    private function processLog(AbstractParaunitProcess $process, Log $logItem): void
    {
        foreach ($this->parsers as $resultContainer) {
            if ($resultContainer->handleLogItem($process, $logItem) instanceof TestResultInterface) {
                return;
            }
        }
    }

    /**
     * @param Log[] $logs
     */
    private function noTestsExecuted(AbstractParaunitProcess $process, array $logs): bool
    {
        return $process->getExitCode() === 0 && count($logs) === 1;
    }
}
