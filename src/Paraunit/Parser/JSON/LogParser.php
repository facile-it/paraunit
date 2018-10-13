<?php

declare(strict_types=1);

namespace Paraunit\Parser\JSON;

use Paraunit\Lifecycle\ProcessEvent;
use Paraunit\Process\AbstractParaunitProcess;
use Paraunit\TestResult\Interfaces\TestResultHandlerInterface;
use Paraunit\TestResult\Interfaces\TestResultInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Class LogParser
 */
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

    /**
     * LogParser constructor.
     *
     * @param LogFetcher $logLocator
     * @param TestResultHandlerInterface $noTestExecutedResultContainer
     * @param EventDispatcherInterface $eventDispatcher
     * @param RetryParser $retryParser
     */
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

    /**
     * @param ParserChainElementInterface $container
     */
    public function addParser(ParserChainElementInterface $container)
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

    /**
     * @param ProcessEvent $processEvent
     */
    public function onProcessTerminated(ProcessEvent $processEvent)
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

    /**
     * @param AbstractParaunitProcess $process
     * @param \stdClass $logItem
     */
    private function processLog(AbstractParaunitProcess $process, \stdClass $logItem)
    {
        /** @var ParserChainElementInterface $resultContainer */
        foreach ($this->parsers as $resultContainer) {
            if ($resultContainer->handleLogItem($process, $logItem) instanceof TestResultInterface) {
                return;
            }
        }
    }

    /**
     * @param AbstractParaunitProcess $process
     * @param array $logs
     *
     * @return bool
     */
    private function noTestsExecuted(AbstractParaunitProcess $process, array $logs): bool
    {
        return $process->getExitCode() === 0 && count($logs) === 1;
    }
}
