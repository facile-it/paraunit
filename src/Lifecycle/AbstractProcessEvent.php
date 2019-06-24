<?php

declare(strict_types=1);

namespace Paraunit\Lifecycle;

use Paraunit\Process\AbstractParaunitProcess;
use Symfony\Component\EventDispatcher\Event;

abstract class AbstractProcessEvent extends Event
{
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
