<?php

declare(strict_types=1);

namespace Paraunit\Lifecycle;

use Paraunit\Process\AbstractParaunitProcess;
use Symfony\Component\EventDispatcher\Event;

class ProcessEvent extends Event
{
    public const PROCESS_STARTED = 'process_event.process_started';

    public const PROCESS_TERMINATED = 'process_event.process_terminated';

    public const PROCESS_TO_BE_RETRIED = 'process_event.process_to_be_retried';

    public const PROCESS_PARSING_COMPLETED = 'process_event.process_parsing_completed';

    /** @var AbstractParaunitProcess */
    private $process;

    public function __construct(AbstractParaunitProcess $process)
    {
        $this->process = $process;
    }

    public function getProcess(): AbstractParaunitProcess
    {
        return $this->process;
    }
}
