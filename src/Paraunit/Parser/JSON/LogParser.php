<?php

declare(strict_types=1);

namespace Paraunit\Parser\JSON;

use Paraunit\Lifecycle\ProcessEvent;
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

    public static function getSubscribedEvents(): array
    {
        return [
            ProcessEvent::PROCESS_TERMINATED => 'onProcessTerminated',
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

    public function onProcessTerminated(ProcessEvent $processEvent): void
    {
        $process = $processEvent->getProcess();
        $logs = $this->logLocator->fetch($process);

        if ($this->noTestsExecuted($process, $logs)) {
            $this->noTestExecutedResultContainer->addProcessToFilenames($process);

            return;
        }

        if ($this->retryParser->processWillBeRetried($process, $logs)) {
            $this->eventDispatcher->dispatch(ProcessEvent::PROCESS_TO_BE_RETRIED, new ProcessEvent($process));

            return;
        }

        foreach ($logs as $singleLog) {
            $this->processLog($process, $singleLog);
        }

        $this->eventDispatcher->dispatch(ProcessEvent::PROCESS_PARSING_COMPLETED, new ProcessEvent($process));
    }

    private function processLog(AbstractParaunitProcess $process, \stdClass $logItem): void
    {
        /** @var ParserChainElementInterface $resultContainer */
        foreach ($this->parsers as $resultContainer) {
            if ($resultContainer->handleLogItem($process, $logItem) instanceof TestResultInterface) {
                return;
            }
        }
    }

    private function noTestsExecuted(AbstractParaunitProcess $process, array $logs): bool
    {
        return $process->getExitCode() === 0 && count($logs) === 1;
    }
}
