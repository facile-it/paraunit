<?php
declare(strict_types=1);

namespace Paraunit\Parser\JSON;

use Paraunit\Lifecycle\ProcessEvent;
use Paraunit\Process\AbstractParaunitProcess;
use Paraunit\TestResult\Interfaces\TestResultBearerInterface;
use Paraunit\TestResult\Interfaces\TestResultHandlerInterface;
use Paraunit\TestResult\Interfaces\TestResultInterface;

/**
 * Class LogParser
 * @package Paraunit\Parser\JSON
 */
class LogParser
{
    /** @var LogFetcher */
    private $logLocator;

    /** @var ParserChainElementInterface[] */
    private $parsers;

    /** @var TestResultHandlerInterface */
    private $noTestExecutedResultContainer;

    /**
     * LogParser constructor.
     * @param LogFetcher $logLocator
     * @param TestResultHandlerInterface $noTestExecutedResultContainer
     */
    public function __construct(LogFetcher $logLocator, TestResultHandlerInterface $noTestExecutedResultContainer)
    {
        $this->logLocator = $logLocator;
        $this->noTestExecutedResultContainer = $noTestExecutedResultContainer;
        $this->parsers = [];
    }

    /**
     * @param ParserChainElementInterface $container
     */
    public function addParser(ParserChainElementInterface $container)
    {
        $this->parsers[] = $container;
    }

    /**
     * @return TestResultBearerInterface[]
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

        foreach ($logs as $singleLog) {
            $this->processLog($process, $singleLog);
        }
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
     * @return bool
     */
    private function noTestsExecuted(AbstractParaunitProcess $process, array $logs): bool
    {
        return $process->getExitCode() === 0 && count($logs) === 1;
    }
}
