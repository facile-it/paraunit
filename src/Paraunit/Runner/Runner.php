<?php

namespace Paraunit\Runner;

use Paraunit\Configuration\PHPUnitConfig;
use Paraunit\Printer\DebugPrinter;
use Paraunit\Process\AbstractParaunitProcess;
use Paraunit\Process\ParaunitProcessInterface;
use Paraunit\Process\ProcessFactory;
use Paraunit\Lifecycle\EngineEvent;
use Paraunit\Lifecycle\ProcessEvent;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * Class Runner.
 */
class Runner
{
    /** @var int */
    protected $maxProcessNumber;

    /** @var AbstractParaunitProcess[] */
    protected $processStack;

    /** @var AbstractParaunitProcess[] */
    protected $processCompleted;

    /** @var AbstractParaunitProcess[] */
    protected $processRunning;

    /** @var  ProcessFactory */
    protected $processFactory;

    /** @var EventDispatcherInterface */
    protected $eventDispatcher;

    /**
     * @param int $maxProcessNumber
     * @param EventDispatcherInterface $eventDispatcher
     * @param ProcessFactory $processFactory
     */
    public function __construct(
        EventDispatcherInterface $eventDispatcher,
        ProcessFactory $processFactory,
        $maxProcessNumber = 10
    ) {

        $this->eventDispatcher = $eventDispatcher;
        $this->maxProcessNumber = $maxProcessNumber;
        $this->processFactory = $processFactory;

        $this->processStack = array();
        $this->processCompleted = array();
        $this->processRunning = array();
    }

    /**
     * @param                 $files
     * @param OutputInterface $outputInterface
     * @param PHPUnitConfig $phpunitConfigFile
     * @param bool $debug
     * @return int
     */
    public function run($files, OutputInterface $outputInterface, PHPUnitConfig $phpunitConfigFile, $debug = false)
    {
        $this->eventDispatcher->dispatch(EngineEvent::BEFORE_START, new EngineEvent($outputInterface));

        $this->processFactory->setConfigFile($phpunitConfigFile);
        $start = new \Datetime('now');
        $this->createProcessStackFromFiles($files);

        $this->eventDispatcher->dispatch(
            EngineEvent::START,
            new EngineEvent($outputInterface, array('start' => $start,))
        );

        while (! empty($this->processStack) || ! empty($this->processRunning)) {
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
    protected function getReturnCode()
    {
        foreach ($this->processCompleted as $process) {
            if ($process->getExitCode() != 0) {
                return 10;
            }
        }

        return 0;
    }

    /**
     * @param string[] $files
     */
    protected function createProcessStackFromFiles($files)
    {
        foreach ($files as $file) {
            $process = $this->processFactory->createProcess($file);
            $this->processStack[$process->getUniqueId()] = $process;
        }
    }

    /**
     * @param $debug
     *
     * @return AbstractParaunitProcess
     */
    protected function runProcess($debug)
    {
        if ($this->maxProcessNumber > count($this->processRunning) && ! empty($this->processStack)) {
            /** @var ParaunitProcessInterface $process */
            $process = array_pop($this->processStack);
            $process->start();
            $this->processRunning[$process->getUniqueId()] = $process;

            if ($debug) {
                DebugPrinter::printDebugOutput($process, $this->processRunning);
            }

            return $process;
        }
    }

    /**
     * @param AbstractParaunitProcess $process
     * @throws \Exception
     */
    protected function markProcessCompleted(AbstractParaunitProcess $process)
    {
        $pHash = $process->getUniqueId();

        if (array_key_exists($pHash, $this->processRunning)) {
            unset($this->processRunning[$pHash]);
        } else {
            throw new \Exception('Trying to remove a non-existing process from running stack\! ID: ' . $pHash);
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
