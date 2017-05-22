<?php

namespace Paraunit\Lifecycle;

use Paraunit\Process\AbstractParaunitProcess;
use Paraunit\Process\ParaunitProcessInterface;

/***
 * Class ProcessEvent
 * @package Paraunit\Lifecycle
 */
class ProcessEvent extends AbstractEvent
{
    const PROCESS_STARTED = 'process_event.process_started';
    const PROCESS_TERMINATED = 'process_event.process_terminated';

    /** @var ParaunitProcessInterface */
    private $process;

    /**
     * @param AbstractParaunitProcess $process
     * @param array $context
     */
    public function __construct(AbstractParaunitProcess $process, $context = array())
    {
        parent::__construct($context);
        $this->process = $process;
    }

    public function getProcess(): AbstractParaunitProcess
    {
        return $this->process;
    }
}
