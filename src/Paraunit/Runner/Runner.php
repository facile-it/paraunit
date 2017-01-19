<?php

namespace Paraunit\Runner;

use Paraunit\Filter\Filter;
use Paraunit\Lifecycle\EngineEvent;
use Paraunit\Lifecycle\ProcessEvent;
use Paraunit\Printer\DebugPrinter;
use Paraunit\Process\AbstractParaunitProcess;
use Paraunit\Process\ParaunitProcessInterface;
use Paraunit\Process\ProcessFactory;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * Class Runner
 * @package Paraunit\Runner
 */
class Runner
{
    /** @var int */
    private $maxProcessNumber;

    /** @var AbstractParaunitProcess[] */
    private $processStack;

    /** @var AbstractParaunitProcess[] */
    private $processCompleted;

    /** @var AbstractParaunitProcess[] */
    private $processRunning;

    /** @var  ProcessFactory */
    private $processFactory;

    /** @var EventDispatcherInterface */
    private $eventDispatcher;

    /** @var Filter */
    private $filter;

    /**
     * @param EventDispatcherInterface $eventDispatcher
     * @param ProcessFactory $processFactory
     * @param Filter $filter
     * @param int $maxProcessNumber
     */
    public function __construct(
        EventDispatcherInterface $eventDispatcher,
        ProcessFactory $processFactory,
        Filter $filter,
        $maxProcessNumber = 10
    ) {

        $this->eventDispatcher = $eventDispatcher;
        $this->processFactory = $processFactory;
        $this->filter = $filter;
        $this->maxProcessNumber = $maxProcessNumber;

        $this->processStack = array();
        $this->processCompleted = array();
        $this->processRunning = array();
    }

    /**
     * @param OutputInterface $outputInterface
     * @param bool $debug
     * @return int
     */
    public function run(OutputInterface $outputInterface, $debug = false)
    {
        $this->eventDispatcher->dispatch(EngineEvent::BEFORE_START, new EngineEvent($outputInterface));
        $start = new \Datetime('now');

        $files = $this->filter->filterTestFiles();
        $this->createProcessStackFromFiles($files);

        $this->eventDispatcher->dispatch(
            EngineEvent::START,
            new EngineEvent($outputInterface, array('start' => $start,))
        );

        while (count($this->processStack) > 0 || count($this->processRunning) > 0) {
            if ($process = $this->runProcess($debug)) {
                $this->eventDispatcher->dispatch(ProcessEvent::PROCESS_STARTED, new ProcessEvent($process));
            }

            foreach ($this->processRunning as $process) {
                if ($process->isTerminated()) {
                    $this->eventDispatcher->dispatch(
                        ProcessEvent::PROCESS_TERMINATED,
                        new ProcessEvent($process, array('output_interface' => $outputInterface,))
                    );
                    // Completed or back to the stack
                    $this->markProcessCompleted($process);
                }

                usleep(500);
            }
        }

        $end = new \Datetime('now');

        $this->eventDispatcher->dispatch(
            EngineEvent::END,
            new EngineEvent(
                $outputInterface,
                array('end' => $end, 'start' => $start, 'process_completed' => $this->processCompleted)
            )
        );

        return $this->getReturnCode();
    }

    /**
     * @return int
     */
    private function getReturnCode()
    {
        foreach ($this->processCompleted as $process) {
            if ($process->getExitCode() !== 0) {
                return 10;
            }
        }

        return 0;
    }

    /**
     * @param string[] $files
     */
    private function createProcessStackFromFiles($files)
    {
        foreach ($files as $file) {
            $process = $this->processFactory->createProcess($file);
            $this->processStack[$process->getUniqueId()] = $process;
        }
    }

    /**
     * @param $debug
     *
     * @return ParaunitProcessInterface | null
     */
    private function runProcess($debug)
    {
        if ($this->maxProcessNumber > count($this->processRunning) && count($this->processStack) > 0) {
            /** @var ParaunitProcessInterface $process */
            $process = array_pop($this->processStack);
            $process->start();
            $this->processRunning[$process->getUniqueId()] = $process;

            if ($debug) {
                DebugPrinter::printDebugOutput($process, $this->processRunning);
            }

            return $process;
        }

        return null;
    }

    /**
     * @param AbstractParaunitProcess $process
     * @throws \RuntimeException
     */
    private function markProcessCompleted(AbstractParaunitProcess $process)
    {
        $pHash = $process->getUniqueId();

        if (array_key_exists($pHash, $this->processRunning)) {
            unset($this->processRunning[$pHash]);
        } else {
            throw new \RuntimeException('Trying to remove a non-existing process from running stack\! ID: ' . $pHash);
        }

        if ($process->isToBeRetried()) {
            $process->reset();
            $process->increaseRetryCount();
            $this->processStack[$pHash] = $process;
        } else {
            $this->processCompleted[$pHash] = $process;
        }
    }
}
