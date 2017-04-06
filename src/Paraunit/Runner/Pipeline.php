<?php

namespace Paraunit\Runner;

use Paraunit\Lifecycle\ProcessEvent;
use Paraunit\Process\ParaunitProcessInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * Class Pipeline
 * @package Paraunit\Runner
 */
class Pipeline
{
    const ENV_VAR_NAME_PIPELINE_NUMBER = 'PARAUNIT_PIPELINE_NUMBER';

    /** @var EventDispatcherInterface */
    private $dispatcher;

    /** @var ParaunitProcessInterface */
    private $process;

    /** @var int */
    private $number;

    /**
     * Pipeline constructor.
     * @param EventDispatcherInterface $dispatcher
     * @param int $number
     */
    public function __construct(EventDispatcherInterface $dispatcher, $number)
    {
        $this->dispatcher = $dispatcher;
        $this->number = $number;
    }

    public function execute(ParaunitProcessInterface $process)
    {
        $this->process = $process;
        $this->process->start(array(
            self::ENV_VAR_NAME_PIPELINE_NUMBER => $this->number,
        ));
    }

    /**
     * @return bool
     */
    public function isFree()
    {
        return $this->process === null;
    }

    /**
     * @return bool
     * @throws \RuntimeException If the pipeline is empty
     */
    public function isTerminated()
    {
        if ($this->isFree()) {
            throw new \RuntimeException('Check termination on an empty pipeline');
        }

        if ($this->process->isTerminated()) {
            $this->handleProcessTermination();

            return true;
        }

        return false;
    }

    /**
     * @return ParaunitProcessInterface
     */
    public function waitCompletion()
    {
        if ($this->isFree()) {
            throw new \RuntimeException('Waiting on an empty pipeline');
        }

        $this->process->wait();

        $process = $this->process;
        $this->handleProcessTermination();

        return $process;
    }

    /**
     * @return int
     */
    public function getNumber()
    {
        return $this->number;
    }

    private function handleProcessTermination()
    {
        $this->dispatcher->dispatch(ProcessEvent::PROCESS_TERMINATED, new ProcessEvent($this->process));
        $this->process = null;
    }
}
