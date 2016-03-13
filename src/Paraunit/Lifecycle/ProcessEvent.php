<?php

namespace Paraunit\Lifecycle;

use Paraunit\Process\ParaunitProcessAbstract;
use Paraunit\Process\ParaunitProcessInterface;
use Symfony\Component\EventDispatcher\Event;

/***
 * Class ProcessEvent
 * @package Paraunit\Lifecycle
 */
class ProcessEvent extends AbstractEvent
{
    const PROCESS_STARTED = 'process_event.process_started';

    const PROCESS_TERMINATED = 'process_event.process_terminated';

    /** @var ParaunitProcessInterface */
    protected $process;

    /** @var  array */
    protected $context;

    /**
     * @param ParaunitProcessAbstract $process
     * @param array                   $context
     */
    public function __construct(ParaunitProcessAbstract $process, $context = array())
    {
        $this->process = $process;
        $this->context = $context;
    }

    /**
     * @return ParaunitProcessAbstract
     */
    public function getProcess()
    {
        return $this->process;
    }
}
