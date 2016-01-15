<?php

namespace Paraunit\Runner;

use Paraunit\Configuration\PHPUnitConfigFile;
use Paraunit\Printer\DebugPrinter;
use Paraunit\Process\ParaunitProcessAbstract;
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

    /** @var ParaunitProcessAbstract[] */
    protected $processStack;

    /** @var ParaunitProcessAbstract[] */
    protected $processCompleted;

    /** @var ParaunitProcessAbstract[] */
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
        $maxProcessNumber = 10,
        EventDispatcherInterface $eventDispatcher,
        ProcessFactory $processFactory
    )
    {
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
     * @param PHPUnitConfigFile $phpunitConfigFile
     * @param bool $debug
     * @return int
     */
    public function run($files, OutputInterface $outputInterface, PHPUnitConfigFile $phpunitConfigFile, $debug = false)
    {
        $this->eventDispatcher->dispatch(EngineEvent::BEFORE_START, new EngineEvent($files, $outputInterface));

        $this->processFactory->setConfigFile($phpunitConfigFile);
        $start = new \Datetime('now');
        $this->createProcessStackFromFiles($files);

        $this->eventDispatcher->dispatch(
            EngineEvent::START,
            new EngineEvent($files, $outputInterface, array('start' => $start,))
        );

        while ( ! empty($this->processStack) || ! empty($this->processRunning)) {

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
                $files,
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
     * @return ParaunitProcessAbstract
     */
    protected function runProcess($debug)
    {
        if ($this->maxProcessNumber > count($this->processRunning) && ! empty($this->processStack)) {
            /** @var ParaunitProcessInterface $process */
            $process = array_pop($this->processStack);
            $process->start();
            $this->processRunning[md5($process->getCommandLine())] = $process;

            if ($debug) {
                DebugPrinter::printDebugOutput($process, $this->processRunning);
            }

            return $process;
        }
    }

    /**
     * @param ParaunitProcessAbstract $process
     */
    protected function markProcessCompleted(ParaunitProcessAbstract $process)
    {
        $pHash = $process->getUniqueId();
        unset($this->processRunning[$pHash]);

        if ($process->isToBeRetried()) {
            $process->reset();
            $process->increaseRetryCount();
            $this->processStack[$pHash] = $process;
        } else {
            $this->processCompleted[$pHash] = $process;
        }
    }
}
