<?php

namespace Paraunit\Runner;

use Paraunit\Printer\DebugPrinter;
use Paraunit\Printer\SharkPrinter;
use Paraunit\Process\ParaunitProcessAbstract;
use Paraunit\Process\ParaunitProcessInterface;
use Paraunit\Process\SymfonyProcessWrapper;
use Paraunit\Lifecycle\EngineEvent;
use Paraunit\Lifecycle\ProcessEvent;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * Class Runner.
 */
class Runner
{
    // I'm using Paraunit as a vendor package
    const PHPUNIT_RELPATH_FOR_VENDOR = '/../../../../../phpunit/phpunit/phpunit';
    // I'm using Paraunit standalone (developing)
    const PHPUNIT_RELPATH_FOR_STANDALONE = '/../../../vendor/phpunit/phpunit/phpunit';

    /** @var SharkPrinter */
    protected $sharkPrinter;

    /** @var int */
    protected $maxProcessNumber;

    /** @var ParaunitProcessAbstract[] */
    protected $processStack;

    /** @var ParaunitProcessAbstract[] */
    protected $processCompleted;

    /** @var ParaunitProcessAbstract[] */
    protected $processRunning;

    /** @var  string */
    protected $phpunitConfigFile;

    /** @var bool */
    protected $debugMode;

    /** @var  string */
    protected $phpunitBin;

    /**
     * @param EventDispatcherInterface $eventDispatcher
     *
     * @throws \Exception
     */
    public function __construct(
        EventDispatcherInterface $eventDispatcher
    ) {
        $this->eventDispatcher = $eventDispatcher;
        $this->maxProcessNumber = 10;
        $this->debugMode = false;
        $this->processStack = array();
        $this->processCompleted = array();
        $this->processRunning = array();

        if (file_exists(__DIR__.self::PHPUNIT_RELPATH_FOR_VENDOR)) {
            $this->phpunitBin = __DIR__.self::PHPUNIT_RELPATH_FOR_VENDOR;
        } elseif (file_exists(__DIR__.self::PHPUNIT_RELPATH_FOR_STANDALONE)) {
            $this->phpunitBin = __DIR__.self::PHPUNIT_RELPATH_FOR_STANDALONE;
        } else {
            throw new \Exception('Phpunit not found');
        }
    }

    /**
     * @param array $files
     * @param OutputInterface $outputInterface
     * @return int
     */
    public function run(array $files, OutputInterface $outputInterface)
    {
        $this->eventDispatcher->dispatch(EngineEvent::BEFORE_START, new EngineEvent($files, $outputInterface));

        $start = new \Datetime('now');
        $this->createProcessStackFromFiles($files);

        $this->eventDispatcher->dispatch(
            EngineEvent::START,
            new EngineEvent($files, $outputInterface, array('start' => $start,))
        );

        while (!empty($this->processStack) || !empty($this->processRunning)) {

            if ($process = $this->runProcess()) {
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
     * @param int $number
     */
    public function setMaxProcessNumber($number)
    {
        $this->maxProcessNumber = $number;
    }

    /**
     * @param string $path
     */
    public function setPhpunitConfigFile($path)
    {
        $this->phpunitConfigFile = $path;
    }

    /**
     * Sets debug to true
     */
    public function enableDebugMode()
    {
        $this->debugMode = true;
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
            $process = $this->createProcess($file);
            $this->processStack[md5($process->getCommandLine())] = $process;
        }
    }

    /**
     * @param string $fileName
     *
     * @return ParaunitProcessInterface
     */
    protected function createProcess($fileName)
    {
        $configurationFile = getcwd().'/'.$this->phpunitConfigFile;

        $command =
            $this->phpunitBin.
            ' -c '.$configurationFile.' '.
            ' --colors=never '.
            $fileName.
            ' 2>&1';

        return new SymfonyProcessWrapper($command);
    }

    /**
     * @return ParaunitProcessAbstract
     */
    protected function runProcess()
    {
        if ($this->maxProcessNumber > count($this->processRunning) && !empty($this->processStack)) {
            /** @var ParaunitProcessInterface $process */
            $process = array_pop($this->processStack);
            $process->start();
            $this->processRunning[md5($process->getCommandLine())] = $process;

            if ($this->debugMode) {
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
            $this->processStack[$pHash] = $process;
        } else {
            $this->processCompleted[$pHash] = $process;
        }
    }

}
