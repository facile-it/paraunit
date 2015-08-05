<?php

namespace Paraunit\Runner;

use Paraunit\Parser\ProcessOutputParser;
use Paraunit\Printer\FinalPrinter;
use Paraunit\Printer\ProcessPrinter;
use Paraunit\Printer\SharkPrinter;
use Paraunit\Process\ParaunitProcessAbstract;
use Paraunit\Process\ParaunitProcessInterface;
use Paraunit\Process\SymfonyProcessWrapper;
use Paraunit\Lifecycle\EngineEvent;
use Symfony\Component\Console\Formatter\OutputFormatterStyle;
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

    /**
     * @var RetryManager
     */
    protected $retryManager;

    /**
     * @var ProcessOutputParser
     */
    protected $processOutputParser;

    /**
     * @var SharkPrinter
     */
    protected $sharkPrinter;

    /**
     * @var ProcessPrinter
     */
    protected $processPrinter;

    /**
     * @var FinalPrinter
     */
    protected $finalPrinter;

    /**
     * @var int
     */
    protected $maxProcessNumber;

    /**
     * @var ParaunitProcessAbstract[]
     */
    protected $processStack;

    /**
     * @var ParaunitProcessAbstract[]
     */
    protected $processCompleted;

    /**
     * @var ParaunitProcessAbstract[]
     */
    protected $processRunning;

    /** @var  string */
    protected $phpunitConfigFile;

    /** @var  string */
    protected $phpunitBin;

    /**
     * @param RetryManager $retryManager
     * @param ProcessOutputParser $processOutputParser
     * @param ProcessPrinter $processPrinter
     * @param FinalPrinter $finalPrinter
     * @param int $maxProcessNumber
     *
     * @param EventDispatcherInterface $eventDispatcher
     * @throws \Exception
     */
    public function __construct(
        RetryManager $retryManager,
        ProcessOutputParser $processOutputParser,
        ProcessPrinter $processPrinter,
        FinalPrinter $finalPrinter,
        $maxProcessNumber = 10,
        EventDispatcherInterface $eventDispatcher
    ) {
        $this->retryManager = $retryManager;
        $this->processOutputParser = $processOutputParser;
        $this->processPrinter = $processPrinter;
        $this->finalPrinter = $finalPrinter;
        $this->eventDispatcher = $eventDispatcher;

        $this->maxProcessNumber = $maxProcessNumber;

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
     * @param $files
     * @param OutputInterface $outputInterface
     *
     * @return int
     */
    public function run($files, OutputInterface $outputInterface, $phpunitConfigFile)
    {
        $this->phpunitConfigFile = $phpunitConfigFile;

        $this->eventDispatcher
            ->dispatch(EngineEvent::BEFORE_START, EngineEvent::buildFromContext($files, $outputInterface));

        $start = new \Datetime('now');
        $this->createProcessStackFromFiles($files);

        while (!empty($this->processStack) || !empty($this->processRunning)) {
            $this->runProcess();

            foreach ($this->processRunning as $process) {
                if ($process->isTerminated()) {
                    $this->retryManager->setRetryStatus($process);
                    $this->processOutputParser->evaluateAndSetProcessResult($process);
                    $this->processPrinter->printProcessResult($outputInterface, $process);

                    // Completato o reset e stack
                    $this->markProcessCompleted($process);
                }

                usleep(500);
            }
        }

        $end = new \Datetime('now');

        $this->finalPrinter->printFinalResults($outputInterface, $this->processCompleted, $start->diff($end));

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
            $fileName.
            ' 2>&1';

        return new SymfonyProcessWrapper($command);
    }

    protected function runProcess()
    {
        if ($this->maxProcessNumber > count($this->processRunning) && !empty($this->processStack)) {
            /** @var ParaunitProcessInterface $process */
            $process = array_pop($this->processStack);
            $process->start();
            $this->processRunning[md5($process->getCommandLine())] = $process;
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
